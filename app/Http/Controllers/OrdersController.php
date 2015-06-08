<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Http\Requests;
use SimpleOMS\Order;
use SimpleOMS\Order_Detail;
use SimpleOMS\Order_Status;
use SimpleOMS\Order_Order_Status;
use SimpleOMS\Product_Category;
use Hashids\Hashids;
use Datatables;
use Config;
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
        // Get roles
        $role = Auth::user()->role->name;

        $title = 'List of Orders';

		return view('orders.index', compact('role', 'title'));
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
        $hashids = new Hashids(Config::get('constants.SALT'), Config::get('constants.HLEN'));
        foreach ($categories as $key => $category){
            $category->id = $hashids->encode($category->id);
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

        if ($this->computeTotalAmount($products, $quantities, $unit_prices) == 0){
            Session::flash('error_message', 'Total amount is zero.');
            return Redirect::back()->withInput(Input::all());
        } else {
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
        $hashids = new Hashids(Config::get('constants.SALT'), Config::get('constants.HLEN'));
        foreach ($categories as $key => $category){
            $category->id = $hashids->encode($category->id);
        }

        // Get remaining credit
        $credit = Auth::user()->customer->credit->credit_remaining;

        // Hash id
        $hashids = new Hashids(Config::get('constants.SALT'), Config::get('constants.HLEN'));
        $order->id = $hashids->encode($order->id);

        $title = 'Edit Order';

        return view('orders.create', compact('order', 'errors', 'categories', 'items', 'credit', 'title'));
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

        if ($this->computeTotalAmount($products, $quantities, $unit_prices) == 0)
        {
            Session::flash('error_message', 'Total amount is zero.');
            return Redirect::back()->withInput(Input::all());
        }
        else
        {
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
            return redirect('orders/'.Input::get('hash').'/edit');
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
        // Save status
        Order_Order_Status::create([
            'order_id' => $order->id,
            'status_id' => Order_Status::where('name', 'like', $status)->first()->id,
            'user_id' => Auth::user()->id
        ]);

        // Redirect to list of orders
        Session::flash('success', 'Order with PO Number "'.$order->po_number.'" was '.strtolower($status).'.');
        return redirect('orders');
    }
}