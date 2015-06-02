<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Http\Requests;
use Illuminate\Support\Collection;
use Datatables;
use DB;

class DatatablesController extends Controller {

    /***
     * Get orders
     * @return mixed
     */
    public function getOrders()
    {
        $orders = DB::table('orders_vw')->get();
        $order_collection = new Collection();

        foreach ($orders as $order){
            $order_collection->push([
                'id' => $order->id,
                'po_number' => $order->po_number,
                'order_date' => $order->order_date,
                'pickup_date' => $order->pickup_date,
                'customer' => $order->customer,
                'total_amount' => $order->total_amount,
                'status' => $order->status
            ]);
        }

        return Datatables::of($order_collection)
            ->setRowId('id')
            ->setRowClass('status')
            ->removeColumn('id')
            ->make(true);
    }
}