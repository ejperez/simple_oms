<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Http\Requests;
use SimpleOMS\Order;
use SimpleOMS\Order_Detail;
use SimpleOMS\Order_Status;
use SimpleOMS\Order_Order_Status;
use SimpleOMS\Product_Category;
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
		return view('orders.index');
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

        return view('orders.create', compact('errors', 'categories'));
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
        $total = 0;
        $products = Input::get('product');
        $quantities = Input::get('quantity');
        $unit_prices = Input::get('unit_price');

        if (empty($products)){
            Session::flash('error_message', 'There are no ordered items.');
            return Redirect::back()->withInput(Input::all());
        }

        foreach ($products as $key=>$product){
            $total += $quantities[$key] * $unit_prices[$key];
        }

        if ($total == 0){
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

    public function show(Order $order)
    {
        // Fetch form validation errors
        $errors = Session::get('errors');

        // Get categories
        $categories = Product_Category::orderBy('name')->get();

        return view('orders.create', compact('errors', 'categories'));
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

        return view('orders.edit', compact('order', 'product_details', 'errors', 'categories', 'items'));
    }

    /***
     * @param Requests\StoreOrderRequest $request
     * @param Order $order
     * @return string|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Requests\StoreOrderRequest $request, Order $order)
    {
        // Check if there is selected items
        if (count(Input::get('items')) == 0){
            return response()->json(['responseJSON' => 'Shopping cart is empty.'], 422);
        }

        // Check if user entered quantities
        if (floatval(Input::get('total_amount')) == 0){
            return response()->json(['responseJSON' => 'Total amount is zero. Enter order quantity.'], 422);
        }

        // Update order details
        $order->po_number    = Input::get('po_number');
        $order->updated_by   = Auth::user()->id;
        $order->order_date   = Input::get('order_date');
        $order->pickup_date  = Input::get('pickup_date');
        $order->total_amount = Input::get('total_amount');
        $order->update();

        // Delete related order details
        Order_Detail::where('order_id', '=', $order->id)->delete();

        // Save order details
        foreach (Input::get('items') as $item) {
            $order_details = new Order_Detail();
            $order_details->order_id = $order->id;
            $order_details->product_id = $item['id'];
            $order_details->quantity = $item['quantity'];
            $order_details->save();
        }

        return json_encode(['status' => 'success']);
    }
}