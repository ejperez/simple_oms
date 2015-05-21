<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Http\Requests;
use SimpleOMS\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Datatables;
use Session;
use Input;

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
        // Initialize order and shopping cart
        if (!Session::has('SESS_ORDER')){
            Session::put('SESS_ORDER', [
                'po_number' => '',
                'order_date' => '',
                'pickup_date' => ''
            ]);
        }
        if (!Session::has('SESS_ORDER_DETAILS')) {
            Session::put('SESS_ORDER_DETAILS', []);
        }

        return view('orders.create', Session::get('SESS_ORDER'));
    }

    /**
     * Get products data for jQuery Data Table (AJAX)
     * @return mixed
     */
    public function getProducts()
    {
        // Get category name of products
        $products = \SimpleOMS\Product::where('available', '>', '0')
            ->join('product_category', 'product_category.id', '=', 'category_id')
            ->get([
                'product_category.desc as category',
                'products.id',
                'products.code',
                'products.desc',
                'products.price',
                'products.uom',
                'products.available'
            ])
            ->toArray();

        return Datatables::of(new Collection($products))
            ->setRowId('id')
            ->removeColumn('id')
            ->make(true);
    }

    /***
     * Get order details
     * @return mixed
     */
    public function getCart()
    {
        return Datatables::of(new Collection(Session::get('SESS_ORDER_DETAILS')))
            ->editColumn('quantity', function($detail){
                return '<input type="number" data-available="'.$detail['available'].'" data-id="'.$detail['id'].'" value="'.$detail['quantity'].'"/>';
            })
            ->make(true);
    }

    /***
     * Update order details stored in current session
     * @return string
     */
    public function storeProductItem()
    {
        Session::put('SESS_ORDER', Input::get('order'));
        foreach(Input::get('products') as $product){
            Session::push('SESS_ORDER_DETAILS', $product);
        }
        return json_encode(Input::all('products'));
    }
}