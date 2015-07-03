<?php namespace SimpleOMS\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use SimpleOMS\User;
use SimpleOMS\Order;
use SimpleOMS\Order_Detail;
use SimpleOMS\Order_Order_Status;
use SimpleOMS\Order_Status;

class CreateOrder extends Command implements SelfHandling
{
    protected $po_number;
    protected $order_date;
    protected $pickup_date;
    protected $products;
    protected $quantities;
    protected $unit_prices;
    protected $user;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($po_number, $order_date, $pickup_date, array $product, array $quantity, array $unit_price, User $user)
	{
        $this->po_number    = $po_number;
        $this->order_date   = $order_date;
        $this->pickup_date  = $pickup_date;
        $this->products     = $product;
        $this->quantities   = $quantity;
        $this->unit_prices  = $unit_price;
        $this->user         = $user;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
        // Compute total amount, check zero quantity
        $total = 0;
        foreach ($this->products as $key => $product){
            if ($this->quantities[$key] <= 0){
                return 'Quantity ordered must be more than 0.';
            }
            $total += $this->quantities[$key] * $this->unit_prices[$key];
        }

        // Check if credit is enough to satisfy order
        $credits = $this->user->customer->credit->credit_remaining;
        if ($total > $credits) {
            return "Total amount ($total) exceed remaining credits of customer ($credits).";
        } else {
            $order                  = new Order();
            $order->po_number       = $this->po_number;
            $order->customer_id     = $this->user->customer->id;
            $order->order_date      = $this->order_date;
            $order->pickup_date     = $this->pickup_date;
            $order->save();

            // Save order details
            foreach ($this->products as $key => $product) {
                $order_details              = new Order_Detail();
                $order_details->order_id    = $order->id;
                $order_details->product_id  = $product;
                $order_details->quantity    = $this->quantities[$key];
                $order_details->unit_price  = $this->unit_prices[$key];
                $order_details->save();
            }

            // Save status
            Order_Order_Status::create([
                'order_id'  => $order->id,
                'status_id' => Order_Status::where('name', 'like', 'Pending')->first()->id,
                'user_id'   => $this->user->id
            ]);

            \SimpleOMS\Audit_Log::create(['user_id' => \Auth::user()->id, 'activity' => 'Created order with PO '.$order->po_number, 'data' => json_encode($order->toArray())]);

            return $order;
        }
	}
}