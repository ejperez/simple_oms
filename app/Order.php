<?php namespace SimpleOMS;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

	//
    public function details()
    {
        return $this->hasMany('SimpleOMS\Order_Detail', 'order_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo('SimpleOMS\Order_Status', 'status_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('SimpleOMS\User', 'created_by', 'id');
    }

    public function userUpdate()
    {
        return $this->belongsTo('SimpleOMS\User', 'updated_by', 'id');
    }
}
