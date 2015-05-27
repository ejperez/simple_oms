<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Http\Requests;
use Illuminate\Support\Collection;
use SimpleOMS\Order;
use SimpleOMS\Order_Detail;
use SimpleOMS\Order_Status;
use SimpleOMS\Product;
use Datatables;
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
        // Get status and count of orders
        $summary = Order_Status::select('name', 'html_color', 'css_class', 'icon_path', DB::raw('(select count(*) from orders where status_id=order_status.id) as count'))
            ->get()
            ->toArray();

		return view('orders.index', compact('summary'));
	}

    /**
     * Display create order form
     *
     * @return Response
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Get products data for jQuery Data Table (AJAX)
     * @return mixed
     */
    public function getProductsDataTable()
    {
        $products = Product::all();
        foreach($products as $product){
            $product->category;
        }

        return Datatables::of(new Collection($products->toArray()))
            ->setRowId('id')
            ->removeColumn('id')
            ->make(true);
    }

    /***
     * Get transactions
     * @return mixed
     */
    public function getTransactionsDataTable()
    {
        $orders = Order::all();
        foreach($orders as $key => $order){
            $order->status;
            $order->user;
            $order->userUpdate;
            $details = $order->details;

            foreach($details as $detail){
                $product = $detail->product;
                $product->category;
            }
        }

        return Datatables::of(new Collection($orders->toArray()))
            ->setRowId('id')
            ->removeColumn('id')
            ->setRowClass('status.css_class')
            ->make(true);
    }

    /***
     * Persist order and details to database
     * @return string
     */
    public function storeOrder(Requests\StoreOrderRequest $request)
    {
        // Check if there is selected items
        if (count(Input::get('items')) == 0){
            return response()->json(['responseJSON' => 'Shopping cart is empty.'], 422);
        }

        // Check if user entered quantities
        if (floatval(Input::get('total_amount')) == 0){
            return response()->json(['responseJSON' => 'Total amount is zero. Enter order quantity.'], 422);
        }

        // Save order
        $order               = new Order();
        $order->po_number    = Input::get('po_number');
        $order->created_by   = Auth::user()->id;
        $order->order_date   = Input::get('order_date');
        $order->pickup_date  = Input::get('pickup_date');
        $order->total_amount = Input::get('total_amount');
        $order->status_id    = 1;
        $order->save();

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

    public function editOrder($order)
    {
        $order->status;
        $order->user;
        $order->userUpdate;
        $details = $order->details;

        $product_details = [];
        foreach($details as $detail){
            $product = $detail->product;
            $category = $product->category;

            $product_details[$product->id] = [
                'id'        => $product->id,
                'category'  => $category->desc,
                'code'      => $product->code,
                'desc'      => $product->desc,
                'price'     => $product->price,
                'uom'       => $product->uom,
                'available' => $product->available,
                'quantity'  => $detail->quantity
            ];
        }

        return view('orders.edit', compact('order', 'product_details'));
    }
}