<?php namespace SimpleOMS\Http\Requests;

class StoreOrderRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'po_number'     => 'required|alpha_num|max:50|unique:orders,po_number',
                    'order_date'    => 'required|date',
                    'pickup_date'   => 'required|date|after:order_date',
                    'product'       => 'array',
                    'quantity'      => 'array',
                    'unit_price'    => 'array'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                $order = $this->route('order');

                return [
                    'po_number'     => 'required|alpha_num|max:50|unique:orders,po_number,'.$order->id,
                    'order_date'    => 'required|date',
                    'pickup_date'   => 'required|date|after:order_date',
                    'product'       => 'array',
                    'quantity'      => 'array',
                    'unit_price'    => 'array'
                ];
            }
            default:break;
        }
	}
}
