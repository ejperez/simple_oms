<?php namespace SimpleOMS;

use Illuminate\Database\Eloquent\Model;

class Order_Detail extends Model {

    protected $table = 'order_details';

    public function product()
    {
        return $this->hasOne('SimpleOMS\Product', 'id', 'product_id');
    }
}
