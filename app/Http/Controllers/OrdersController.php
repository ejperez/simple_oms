<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Customer;
use SimpleOMS\Http\Requests;
use SimpleOMS\Order;
use SimpleOMS\Order_Status;
use SimpleOMS\Product_Category;
use SimpleOMS\Commands\CreateOrder;
use SimpleOMS\Commands\UpdateOrder;
use SimpleOMS\Commands\UpdateOrderStatus;
use Datatables;
use Redirect;
use Session;
use Input;
use Auth;
use DB;

class OrdersController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        \DB::enableQueryLog();

        // Get query parameters
        $filters = json_decode(Input::get('f'));
        $sort_column = Input::has('s') ? Input::get('s') : 'created_at';
        $sort_direction = Input::has('d') ? Input::get('d') : 'desc';

        // Query view
        $orders = DB::table('orders_vw');

        // Sales can only view his/her orders only
        if (Auth::user()->role->name == 'Sales'){
            $orders->where('customer', '=', Auth::user()->customer->fullName());
        }

        // Search parameters
        if (isset($filters->po_number) && !empty($filters->po_number))
            $orders->where('po_number', 'like', "%$filters->po_number%");

        if (isset($filters->created_at) && !empty($filters->created_at))
            $orders->where('created_at', '=', $filters->created_at);

        if (isset($filters->order_date) && !empty($filters->order_date))
            $orders->where('order_date', '=', $filters->order_date);

        if (isset($filters->pickup_date) && !empty($filters->pickup_date))
            $orders->where('pickup_date', 'like', $filters->pickup_date);

        if (isset($filters->customer) && !empty($filters->customer))
            $orders->where('customer', 'like', "%$filters->customer%");

        // For approvers, pending orders are selected by default
        if (Auth::user()->role->name == 'Approver'){
            if (isset($filters->status) && !empty($filters->status)) {
                $orders->whereIn('status', $filters->status);
            } else if (!isset($filters)){
                $orders->whereIn('status', ['Pending']);
            }
        } else {
            if (isset($filters->status) && !empty($filters->status)) {
                $orders->whereIn('status', $filters->status);
            }
        }

        // Sorting
        $orders = $orders->orderBy($sort_column, $sort_direction)
            ->paginate(PER_PAGE);

        // Get role
        $role = Auth::user()->role->name;

        // Get order status
        $status = Order_Status::all();

		return view('orders.index', compact('orders', 'role', 'status', 'filters', 'sort_column', 'sort_direction'));
	}

    /**
     * Display create order form
     *
     * @return Response
     */
    public function create()
    {
        // Fetch form validation errors
        $errors = Session::get('errors');

        // Get categories
        $categories = Product_Category::orderBy('name')->get();

        // Get remaining credit
        $credit = Auth::user()->customer->credit->credit_remaining;

        return view('orders.create', compact('errors', 'categories', 'credit'));
    }

    /***
     * Persist order and details to database
     * @return string
     */
    //public function storeOrder(Requests\StoreOrderRequest $request)
    public function store(Requests\StoreOrderRequest $request)
    {
        $response = $this->dispatchFrom(CreateOrder::class, $request, [
            'user' => Auth::user()
        ]);

        if ($response instanceof Order){
            Session::flash('success', 'Order with PO Number "'.$response->po_number.'" was created.');
            return redirect('orders/create');
        } else {
            Session::flash('error_message', $response);
            return Redirect::back()->withInput(Input::all());
        }
    }

    /***
     * Load edit order page
     * @param $order
     * @return \Illuminate\View\View
     */
    public function edit(Order $order)
    {
        // Only pending orders can be edited
        $current_status = $order->status()->orderBy('created_at', 'desc')->first()->status->name;
        if ($current_status != 'Pending'){
            Session::flash('error_message', "Unable to edit order. Current status is not pending ($current_status).");
            return Redirect::back()->withInput(Input::all());
        }

        // Get order items
        $details = $order->details;

        $items = [];

        foreach ($details as $detail){
            $product = $detail->product;

            $items[$product->id] = [
                'category' => $product->category->name,
                'name' => $product->name,
                'id' => $product->id,
                'uom' => $product->uom,
                'unit_price' => $detail->unit_price,
                'quantity' => $detail->quantity
            ];
        }

        // Fetch form validation errors
        $errors = Session::get('errors');

        // Get categories
        $categories = Product_Category::orderBy('name')->get();

        // Get role of current user
        $role = Auth::user()->role->name;

        // Get remaining credit of customer of order
        if ($order->customer_id == Auth::user()->id){
            $credit = Auth::user()->customer->credit->credit_remaining;
            return view('orders.create', compact('order', 'errors', 'categories', 'items', 'credit', 'role'));
        } else {
            $customer = Customer::find($order->customer_id);
            $credit = $customer->credit->credit_remaining;
            return view('orders.create', compact('order', 'errors', 'categories', 'items', 'customer', 'credit', 'role'));
        }
    }

    /***
     * Update order and details
     * @param Requests\StoreOrderRequest $request
     * @param Order $order
     * @return string|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Requests\StoreOrderRequest $request, Order $order)
    {
        $response = $this->dispatchFrom(UpdateOrder::class, $request, [
            'user'  => Auth::user(),
            'order' => $order
        ]);

        if ($response instanceof Order){
            Session::flash('success', 'Order with PO Number "'.$order->po_number.'" was updated.');
            if (Auth::user()->role->name == 'Approver'){
                return redirect('orders/'.Input::get('hash').'/edit/approver');
            } else {
                return redirect('orders/'.Input::get('hash').'/edit');
            }
        } else {
            Session::flash('error_message', $response);
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function updateStatus(Order $order, $status)
    {
        $response = $this->dispatch(new UpdateOrderStatus($order, Auth::user(), $status, Input::get('extra')));

        if ($response instanceof Order){
            Session::flash('success', 'Order with PO Number "'.$order->po_number.'" was '.strtolower($status).'.');
            return redirect('orders');
        } else {
            Session::flash('error_message', $response);
            return Redirect::back()->withInput(Input::all());
        }
    }
}