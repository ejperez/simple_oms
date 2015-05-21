<?php namespace SimpleOMS;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
	//
    public function category()
    {
        return $this->belongsTo('SimpleOMS\Product_Category', 'id', 'category_id');
    }

}
