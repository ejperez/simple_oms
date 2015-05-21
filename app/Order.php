<?php namespace SimpleOMS;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

	//
    public function details()
    {
        $this->hasMany('SimpleOMS\Order_Details', 'order_id', 'id');
    }
}
