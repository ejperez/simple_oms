<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Product_Category;
use SimpleOMS\Http\Requests;
use SimpleOMS\Order;
use SimpleOMS\User;
use DB;

class AJAXController extends Controller {

    public function searchProductByCategory(Product_Category $category)
    {
        $category->products;
        return json_encode($category);
	}

    public function getUserOrderCountStatus(User $user)
    {
        $data = DB::table('orders_vw')
            ->where('customer_id', '=', $user->id)
            ->groupBy('status')
            ->select([
                'status as label',
                DB::raw('count(*) as value')
            ])
            ->get();

        return json_encode($data);
    }

    public function getOrderDetails(Order $order)
    {
        $details = $order->details;
        $details_array = [];
        foreach ($details as $detail){
            $product = $detail->product;
            $category = $product->category;

            $details_array[] = [
                'product' => $product->name,
                'category' => $category->name,
                'unit_price' => $detail->unit_price,
                'quantity' => $detail->quantity,
                'uom' => $product->uom,
                'price' => $detail->getPrice()
            ];
        }
        $userUpdate = $order->userUpdate;
        if (isset($userUpdate)){
            $customer = $userUpdate->customer;
        }

        $latest_status = $order->latestStatus();

        return json_encode([
            'po_number' => $order->po_number,
            'customer' => $order->customer->fullName(),
            'credits' => $order->customer->credit->credit(),
            'updated_by' => isset($userUpdate) ? $customer->fullName() : null,
            'updated_at' => isset($userUpdate) ? $order->updated_at->format(DATE_FORMAT_PHP) : null,
            'update_remarks' => isset($userUpdate) ? $order->update_remarks : null,
            'details' => $details_array,
            'status' => $latest_status->status->name,
            'user' => $latest_status->user->customer->fullName(),
            'extra' => $latest_status->extra,
            'total' => $order->totalAmount(),
        ]);
    }
}