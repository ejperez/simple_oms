<?php namespace SimpleOMS;

use Illuminate\Database\Eloquent\Model;

class Product_Category extends Model {

    protected $table = 'product_category';

    public $timestamps = false;

    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany('SimpleOMS\Product', 'category_id', 'id');
    }
}
