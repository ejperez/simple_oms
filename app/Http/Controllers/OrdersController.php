<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Customer;
use SimpleOMS\Http\Requests;
use SimpleOMS\Order;
use SimpleOMS\Order_Detail;
use SimpleOMS\Order_Status;
use SimpleOMS\Order_Order_Status;
use SimpleOMS\Product_Category;
use SimpleOMS\Helpers\Helpers;
use Datatables;
use Redirect;
use Session;
use Input;
use Auth;

class OrdersController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        // Get role
        $role = Auth::user()->role->name;

        // Get order status
        $status = Order_Status::all();

        $title = 'List of Orders';

		return view('orders.index', compact('role', 'title', 'status'));
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

        // Hash categories
        foreach ($categories as $key => $category){
            $category->id = Helpers::hash($category->id);
        }

        // Get remaining credit
        $credit = Auth::user()->customer->credit->credit_remaining;

        $title = 'Create Order';

        return view('orders.create', compact('errors', 'categories', 'credit', 'title'));
    }

    /***
     * Persist order and details to database
     * @return string
     */
    //public function storeOrder(Requests\StoreOrderRequest $request)
    public function store(Requests\StoreOrderRequest $request)
    {
        // Compute total amount
        // Order with total amount of zero must not proceed
        $products = Input::get('product');
        $quantities = Input::get('quantity');
        $unit_prices = Input::get('unit_price');

        $credits = Auth::user()->customer->credit->credit_remaining;
        $total = $this->computeTotalAmount($products, $quantities, $unit_prices);

        if ($total <= 0){
            Session::flash('error_message', 'Total amount is equal to or less than zero.');
            return Redirect::back()->withInput(Input::all());
        } else {
            if ($total > $credits){
                Session::flash('error_message', "Total amount ($total) exceed remaining credits of customer ($credits).");
                return Redirect::back()->withInput(Input::all());
            }

            // Save to database
            $order               = new Order();
            $order->po_number    = Input::get('po_number');
            $order->customer_id   = Auth::user()->customer->id;
            $order->order_date   = Input::get('order_date');
            $order->pickup_date  = Input::get('pickup_date');
            $order->save();

            // Save order details
            foreach ($products as $key=>$product) {
                $order_details = new Order_Detail();
                $order_details->order_id = $order->id;
                $order_details->product_id = $product;
                $order_details->quantity = $quantities[$key];
                $order_details->unit_price = $unit_prices[$key];
                $order_details->save();
            }

            // Save status
            Order_Order_Status::create([
                'order_id' => $order->id,
                'status_id' => Order_Status::where('name', 'like', 'Pending')->first()->id,
                'user_id' => Auth::user()->id
            ]);

            // Redirect to create order form
            Session::flash('success', 'Order with PO Number "'.$order->po_number.'" was created.');
            return redirect('orders/create');
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

        // Hash categories
        foreach ($categories as $key => $category){
            $category->id = Helpers::hash($category->id);
        }

        // Hash id
        $order->id = Helpers::hash($order->id);

        // Get role of current user
        $role = Auth::user()->role->name;

        $title = 'Edit Order';

        // Get remaining credit of customer of order
        if ($order->customer_id == Auth::user()->customer_id){
            $credit = Auth::user()->customer->credit->credit_remaining;
            return view('orders.create', compact('order', 'errors', 'categories', 'items', 'credit', 'title', 'role'));
        } else {
            $customer = Customer::find($order->customer_id);
            $credit = $customer->credit->credit_remaining;
            return view('orders.create', compact('order', 'errors', 'categories', 'items', 'customer', 'credit', 'title', 'role'));
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
        $products = Input::get('product');
        $quantities = Input::get('quantity');
        $unit_prices = Input::get('unit_price');

        if ($order->customer_id == Auth::user()->customer_id){
            $credit = Auth::user()->customer->credit->credit_remaining;
        } else {
            // Check if extra field is not empty, if edited by other user
            if (trim(Input::get('extra')) == ''){
                Session::flash('error_message', "Please provide reason for editing.");
                return Redirect::back()->withInput(Input::all());
            } else {
                $order->update_remarks = strip_tags(Input::get('extra'));
            }

            $credit = Customer::find($order->customer_id)->credit->credit_remaining;
        }

        $total = $this->computeTotalAmount($products, $quantities, $unit_prices);

        if ($total <= 0){
            Session::flash('error_message', 'Total amount is equal to or less than zero.');
            return Redirect::back()->withInput(Input::all());
        }  else {
            if ($total > $credit){
                Session::flash('error_message', "Total amount ($total) exceed remaining credits of customer ($credit).");
                return Redirect::back()->withInput(Input::all());
            }

            $order->po_number    = Input::get('po_number');
            $order->order_date   = Input::get('order_date');
            $order->pickup_date  = Input::get('pickup_date');
            $order->updated_by   = Auth::user()->id;
            $order->update();

            // Delete related order details
            Order_Detail::where('order_id', '=', $order->id)->delete();

            // Save order details
            foreach ($products as $key=>$product) {
                $order_details = new Order_Detail();
                $order_details->order_id = $order->id;
                $order_details->product_id = $product;
                $order_details->quantity = $quantities[$key];
                $order_details->unit_price = $unit_prices[$key];
                $order_details->save();
            }

            // Redirect to update order form
            Session::flash('success', 'Order with PO Number "'.$order->po_number.'" was updated.');
            if (Auth::user()->role->name == 'Approver'){
                return redirect('orders/'.Input::get('hash').'/edit/approver');
            } else {
                return redirect('orders/'.Input::get('hash').'/edit');
            }
        }
    }

    /***
     * Compute total amount of items
     * @param $products
     * @param $quantities
     * @param $unit_prices
     * @return int
     */
    private function computeTotalAmount($products, $quantities, $unit_prices)
    {
        if (is_array($products)){
            $total = 0;
            foreach ($products as $key=>$product){
                $total += $quantities[$key] * $unit_prices[$key];
            }
            return $total;
        } else {
            return 0;
        }
    }

    public function updateStatus(Order $order, $status)
    {
        // Check if current status is pending
        $current_status = $order->status()->orderBy('created_at', 'desc')->first()->status->name;
        if ($current_status != 'Pending'){
            Session::flash('error_message', "Unable to change order status. Current status is not pending ($current_status).");
            return Redirect::back()->withInput(Input::all());
        }

        // For disapproval/cancellation, check if extra is not empty
        if ($status == 'Disapproved' || $status == 'Cancelled'){
            if (trim(Input::get('extra')) == ''){
                Session::flash('error_message', "Please provide reason for cancellation/disapproval.");
                return Redirect::back()->withInput(Input::all());
            }
        }

        // For approval, check if user credits is more than or equal to total amount
        $total = 0;
        if ($status == 'Approved'){
            $details = $order->details;
            $credits = $order->customer->credit->credit_remaining;

            foreach ($details as $detail){
                $total += $detail->quantity * $detail->unit_price;
            }

            if ($total > $credits){
                Session::flash('error_message', "Total amount ($total) exceed remaining credits of customer ($credits).");
                return Redirect::back()->withInput(Input::all());
            }
        }

        // Save status
        Order_Order_Status::create([
            'order_id' => $order->id,
            'status_id' => Order_Status::where('name', 'like', $status)->first()->id,
            'user_id' => Auth::user()->id,
            'extra' => strip_tags(Input::get('extra'))
        ]);

        // Subtract total amount to customer credits
        $credit = Customer::find($order->customer_id)->credit;
        $credit->credit_remaining -= $total;
        $credit->update();

        // Redirect to list of orders
        Session::flash('success', 'Order with PO Number "'.$order->po_number.'" was '.strtolower($status).'.');
        return redirect('orders');
    }
}