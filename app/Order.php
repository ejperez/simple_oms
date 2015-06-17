<?php namespace SimpleOMS;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $fillable = ['customer_id', 'employee_id', 'order_date', 'required_date'];

    // Relationships

    public function details()
    {
        return $this->hasMany('SimpleOMS\Order_Detail', 'order_id', 'id');
    }

    public function status()
    {
        return $this->hasMany('SimpleOMS\Order_Order_Status', 'order_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('SimpleOMS\Customer', 'customer_id', 'id');
    }

    public function userUpdate()
    {
        return $this->belongsTo('SimpleOMS\User', 'updated_by', 'id');
    }

    // Methods

    /**
     * Compute total amount of order
     * @return int
     */
    public function totalAmount()
    {
        $total = 0;
        foreach ($this->details as $detail){
            $total += $detail->quantity * $detail->unit_price;
        }
        return number_format($total,2);
    }

    /**
     * Get current status of order
     * @return mixed
     */
    public function latestStatus()
    {
        return $this->status->sortByDesc('created_at')->first();
    }
}
