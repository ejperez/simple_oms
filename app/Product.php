<?php namespace SimpleOMS;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
	//
    public function category()
    {
        return $this->belongsTo('SimpleOMS\Product_Category', 'category_id', 'id');
    }

    public function order_detail()
    {
        return $this->belongsTo('SimpleOMS\Order_Detail', 'id', 'product_id');
    }
}
