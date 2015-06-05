<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Http\Requests;
use Illuminate\Support\Collection;
use Datatables;
use Auth;
use DB;

class DatatablesController extends Controller {

    /***
     * Get orders
     * @return mixed
     */
    public function getOrders()
    {
        // Approvers can view orders from all customers
        // Administrators and Sales can only view their orders

        if (Auth::user()->hasRole(['approver'])){
            $orders = DB::table('orders_vw')->get();
        } else {
            $orders = DB::table('orders_vw')->where('user_id', '=', Auth::user()->id)->get();
        }

        $order_collection = new Collection();

        foreach ($orders as $order){
            $order_collection->push([
                'id' => $order->id,
                'po_number' => $order->po_number,
                'order_date' => $order->order_date,
                'pickup_date' => $order->pickup_date,
                'customer' => $order->customer,
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'details' => DB::table('order_details_vw')->where('order_id', '=', $order->id)->get()
            ]);
        }

        return Datatables::of($order_collection)
            ->setRowId('id')
            ->setRowClass('status')
            ->make(true);
    }
}