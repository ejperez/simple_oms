<?php namespace SimpleOMS;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $fillable = ['customer_id', 'employee_id', 'order_date', 'required_date'];

    public function details()
    {
        return $this->hasMany('SimpleOMS\Order_Detail', 'order_id', 'id');
    }

    public function status()
    {
        return $this->hasMany('SimpleOMS\Order_Order_Status', 'order_id', 'id');
    }

    public function latestStatus()
    {
        return $this->hasMany('SimpleOMS\Order_Order_Status', 'order_id', 'id')->orderBy('created_at', 'desc')->first();
    }

    public function customer()
    {
        return $this->belongsTo('SimpleOMS\Customer', 'customer_id', 'id');
    }

    public function userUpdate()
    {
        return $this->belongsTo('SimpleOMS\User', 'updated_by', 'id');
    }
}
