<?php namespace SimpleOMS\Http\Controllers;

use Illuminate\Support\Collection;
use SimpleOMS\Helpers\Helpers;
use SimpleOMS\Order;
use SimpleOMS\Http\Requests;
use Datatables;
use Auth;

class DatatablesController extends Controller {

    /***
     * Get orders
     * @return mixed
     */
    public function getOrders()
    {
        //\DB::enableQueryLog();

        // Approvers can view orders from all customers
        // Administrators and Sales can only view their orders

        // Eager loading
        if (Auth::user()->hasRole(['approver'])){
            $orders = Order::with('customer', 'customer.credit', 'details', 'details.product', 'details.product.category', 'status', 'status.status', 'status.user', 'status.user.customer')
                ->get();
        } else {
            $orders = Order::where('customer_id', '=', Auth::user()->customer->id)
                ->with('customer', 'customer.credit', 'details', 'details.product', 'details.product.category', 'status', 'status.status', 'status.user', 'status.user.customer')
                ->get();
        }

        $order_collection = new Collection();

        foreach ($orders as $order) {
            // Get customer
            $customer = $order->customer;

            // Get details and compute total amount
            $details = $order->details;
            $total = 0;
            $order_details = new Collection();
            foreach ($details as $detail){
                $product = $detail->product;
                $category = $product->category;

                $order_details->push([
                    'order_id' =>  Helpers::hash($detail->order_id),
                    'product' => $product->name,
                    'category' => $category->name,
                    'uom' => $product->uom,
                    'unit_price' => $detail->unit_price,
                    'quantity' => $detail->quantity,
                    'price' => ($detail->unit_price * $detail->quantity)
                ]);

                $total += $detail->quantity * $detail->unit_price;
            }

            // Latest status
            $latest_status = $order->status->sortByDesc('created_at')->first();

            $order_collection->push([
                'po_number' => $order->po_number,
                'order_date' => $order->order_date,
                'pickup_date' => $order->pickup_date,
                'customer' => $customer->fullName(),
                'total_amount' => $total,
                'status' => $latest_status->status->name,
                'user' => $latest_status->user->customer->fullName(),
                'extra' => $latest_status->extra,
                'id' => Helpers::hash($order->id),
                'credits' => $customer->credit->credit_remaining,
                'details' => $order_details->toArray()
            ]);
        }

        //var_dump(\DB::getQueryLog());

        //dd($orders->toArray()[0]);
        //dd($order_collection->toArray());

        return Datatables::of($order_collection)
            ->setRowId('id')
            ->addRowAttr('data-po-number', '{{ $po_number }}')
            ->addRowAttr('data-status', '{{ $status }}')
            ->make(true);
    }
}