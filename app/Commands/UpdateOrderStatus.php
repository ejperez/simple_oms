<?php namespace SimpleOMS\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use SimpleOMS\Order;
use SimpleOMS\Order_Order_Status;
use SimpleOMS\User;
use SimpleOMS\Order_Status;
use SimpleOMS\Customer;

class UpdateOrderStatus extends Command implements SelfHandling
{
    protected $order;
    protected $user;
    protected $status;
    protected $extra;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Order $order, User $user, $status, $extra)
	{
        $this->order    = $order;
        $this->user     = $user;
        $this->status   = $status;
        $this->extra    = $extra;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
        // Check if current status is pending
        $current_status = $this->order->status()->orderBy('created_at', 'desc')->first()->status->name;
        if ($current_status != 'Pending'){
            return "Unable to change order status. Current status is not pending ($current_status).";
        }

        // For disapproval/cancellation, check if extra is not empty
        if ($this->status == 'Disapproved' || $this->status == 'Cancelled'){
            if (trim($this->extra) == ''){
                return "Please provide reason for cancellation/disapproval.";
            }
        }

        // For approval, check if user credits is more than or equal to total amount
        $total = 0;
        if ($this->status == 'Approved'){
            $details = $this->order->details;
            $credits = $this->order->customer->credit->credit_remaining;

            foreach ($details as $detail){
                $total += $detail->quantity * $detail->unit_price;
            }

            if ($total > $credits){
                return "Total amount ($total) exceed remaining credits of customer ($credits).";
            }
        }

        // Save status
        Order_Order_Status::create([
            'order_id'  => $this->order->id,
            'status_id' => Order_Status::where('name', 'like', $this->status)->first()->id,
            'user_id'   => $this->user->id,
            'extra'     => strip_tags($this->extra)
        ]);

        // Subtract total amount to customer credits
        $credit = Customer::find($this->order->customer_id)->credit;
        $credit->credit_remaining -= $total;
        $credit->update();

        \SimpleOMS\Audit_Log::create(['user_id' => \Auth::user()->id, 'activity' => 'Updated status of order with PO '.$this->order->po_number.' to '.$this->status, 'data' => null]);

        return $this->order;
	}
}