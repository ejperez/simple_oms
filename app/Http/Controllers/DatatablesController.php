<?php namespace SimpleOMS\Http\Controllers;

use Illuminate\Support\Collection;
use SimpleOMS\Helpers\Helpers;
use SimpleOMS\Order;
use SimpleOMS\Http\Requests;
use Datatables;
use Input;
use Auth;
use DB;

class DatatablesController extends Controller {

    /***
     * Get orders
     * @return mixed
     */
    public function getOrders()
    {
        // Filter orders by requested status
        $requested_status = Input::get('status');

        \DB::enableQueryLog();

        // Approvers and Administrators can view orders from all customers
        // Sales can only view their orders
        // Eager loading
        if (Auth::user()->hasRole(['approver', 'administrator'])){
            //$orders = Order::with('customer', 'customer.credit', 'details', 'details.product', 'details.product.category', 'status', 'status.status', 'status.user', 'status.user.customer', 'userUpdate.customer')
            $orders = Order::with(['customer' => function($query){
                $query->where('first_name', '=', '%El John%');
            }])
                ->take(15)
                ->get();
        } else {
            $orders = Order::where('customer_id', '=', Auth::user()->customer->id)
                ->with('customer', 'customer.credit', 'details', 'details.product', 'details.product.category', 'status', 'status.status', 'status.user', 'status.user.customer', 'userUpdate.customer')
                ->get();
        }

        var_dump(\DB::getQueryLog());

        dd($orders->toArray());

        $order_collection = new Collection();

        foreach ($orders as $order) {

            // Get latest status of order
            $latest_status = $order->status->sortByDesc('created_at')->first();

            // Ensure that only orders with status matching requested status are returned
            if (!empty($requested_status)){
                if (array_search($latest_status->status->name, $requested_status) === FALSE){
                    // Skip this order
                    continue;
                }
            }

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

            $order_collection->push([
                'po_number' => $order->po_number,
                'created_date' => $order->created_at->format('Y-m-d'),
                'order_date' => $order->order_date,
                'pickup_date' => $order->pickup_date,
                'customer' => $customer->fullName(),
                'total_amount' => $total,
                'status' => $latest_status->status->name,
                'user' => $latest_status->user->customer->fullName(),
                'extra' => is_null($latest_status->extra) ? 'No comments provided.' : htmlentities($latest_status->extra),
                'updated_by' => is_null($order->userUpdate) ? null : $order->userUpdate->customer->fullName(),
                'updated_date' => $order->updated_at->format('Y-m-d'),
                'update_remarks' => is_null($order->update_remarks) ? 'No comments provided.' : htmlentities($order->update_remarks),
                'change_status_date' => $latest_status->created_at->format('Y-m-d'),
                'id' => Helpers::hash($order->id),
                'credits' => $customer->credit->credit_remaining,
                'details' => $order_details->toArray()
            ]);
        }

        var_dump(\DB::getQueryLog());

        //dd($orders->toArray()[0]);
        dd($order_collection->toArray()[0]);

        return Datatables::of($order_collection)
            ->setRowId('id')
            ->addRowAttr('data-po-number', '{{ $po_number }}')
            ->addRowAttr('data-status', '{{ $status }}')
            ->make(true);
    }
}