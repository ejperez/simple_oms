<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Product_Category;
use SimpleOMS\Http\Requests;
use SimpleOMS\Order;
use SimpleOMS\User;
use SimpleOMS\Helpers\Helpers;
use DB;

class AJAXController extends Controller {

    public function searchProductByCategory(Product_Category $category)
    {
        $category->products;
        return json_encode($category);
	}

    public function getUserOrderPendingCount(User $user)
    {
        $data = DB::table('orders_vw');

        // If user is approver, get all
        if ($user->hasRole('Approver')){
            $data->where('status', '=', 'Pending');
        } else {
            $data->where('customer_id', '=', $user->id)
                ->where('status', '=', 'Pending', 'AND');
        }

        $data = $data->select([
            'id',
            'po_number',
            'order_date',
            DB::raw('TIMESTAMPDIFF(DAY, NOW(), order_date) as days')
        ])
            ->orderBy('days', 'asc')
            ->get();

        // Segregate nearly expired orders from expired pending orders
        // Limit orders fetched to save performance
        $near_expiration = [];
        $expired = [];
        $limit = 10;

        foreach ($data as $item){
            // Hash id
            $item->id = Helpers::hash($item->id);
            if ($item->days < 0){
                if (count($expired) < $limit){
                    // Use absolute value of days
                    $item->days = abs($item->days);
                    $expired[] = $item;
                }
            } else {
                if (count($near_expiration) < $limit) {
                    $near_expiration[] = $item;
                }
            }
        }

        return json_encode([
            'near_expired' => $near_expiration,
            'expired' => $expired,
            'limit' => $limit
        ]);
    }

    public function getUserOrderCountStatus(User $user)
    {
        // If user is approver, get all
        if ($user->hasRole('Approver')){
            $data = DB::select(DB::raw('SELECT ov.status as label, COUNT(0) AS count, COUNT(0) AS total, ROUND((COUNT(0) / t.cnt * 100), 2) AS value, t.cnt AS total FROM orders_vw ov CROSS JOIN (SELECT COUNT(*) AS cnt FROM orders_vw) t GROUP BY ov.status'));
        } else {
            $data = DB::select(DB::raw('SELECT ov.status as label, COUNT(0) AS count, COUNT(0) AS total, ROUND((COUNT(0) / t.cnt * 100), 2) AS value, t.cnt AS total FROM orders_vw ov CROSS JOIN (SELECT COUNT(*) AS cnt FROM orders_vw WHERE customer_id = ?) t WHERE customer_id = ? GROUP BY ov.status'), [$user->id, $user->id]);
        }

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
            'update_remarks' => isset($userUpdate) ? ($order->update_remarks == '' ? 'No remarks' : $order->update_remarks) : null,
            'details' => $details_array,
            'status' => $latest_status->status->name,
            'user' => $latest_status->user->customer->fullName(),
            'extra' => $latest_status->extra,
            'total' => $order->totalAmount(),
        ]);
    }
}