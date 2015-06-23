<?php namespace SimpleOMS\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use SimpleOMS\User;
use SimpleOMS\Order;
use SimpleOMS\Order_Detail;
use SimpleOMS\Customer;

class UpdateOrder extends Command implements SelfHandling
{
    protected $po_number;
    protected $order_date;
    protected $pickup_date;
    protected $products;
    protected $quantities;
    protected $unit_prices;
    protected $user;
    protected $order;
    protected $extra;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($po_number, $order_date, $pickup_date, array $product, array $quantity, array $unit_price, User $user, Order $order, $extra)
	{
        $this->po_number    = $po_number;
        $this->order_date   = $order_date;
        $this->pickup_date  = $pickup_date;
        $this->products     = $product;
        $this->quantities   = $quantity;
        $this->unit_prices  = $unit_price;
        $this->user         = $user;
        $this->order        = $order;
        $this->extra        = $extra;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
        if ($this->order->customer_id == $this->user->id){
            $credit = $this->user->customer->credit->credit_remaining;
        } else {
            // Check if extra field is not empty, if edited by other user
            if (trim($this->extra) == ''){
                return "Please provide reason for editing.";
            }

            $credit = Customer::find($this->order->customer_id)->credit->credit_remaining;
        }

        // Compute total amount, check zero quantity
        $total = 0;
        foreach ($this->products as $key => $product){
            if ($this->quantities[$key] <= 0){
                return 'Quantity ordered must be more than 0.';
            }
            $total += $this->quantities[$key] * $this->unit_prices[$key];
        }

        // Check if credit is enough to satisfy order
        if ($total > $credit) {
            return "Total amount ($total) exceed remaining credits of customer ($credit).";
        } else {
            $this->order->po_number         = $this->po_number;
            $this->order->order_date        = $this->order_date;
            $this->order->pickup_date       = $this->pickup_date;
            $this->order->updated_by        = $this->user->id;
            $this->order->update_remarks    = strip_tags($this->extra);
            $this->order->update();

            // Delete related order details
            Order_Detail::where('order_id', '=', $this->order->id)->delete();

            // Save order details
            foreach ($this->products as $key => $product) {
                $order_details              = new Order_Detail();
                $order_details->order_id    = $this->order->id;
                $order_details->product_id  = $product;
                $order_details->quantity    = $this->quantities[$key];
                $order_details->unit_price  = $this->unit_prices[$key];
                $order_details->save();
            }

            return $this->order;
        }
	}
}