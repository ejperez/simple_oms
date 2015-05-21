<?php namespace SimpleOMS;

use Illuminate\Database\Eloquent\Model;

class Order_Detail extends Model {

	//
    public function order()
    {
        $this->hasOne('SimpleOMS\Order', 'id', 'order_id');
    }
}
