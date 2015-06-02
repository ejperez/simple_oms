<?php namespace SimpleOMS\Http\Requests;

use SimpleOMS\Http\Requests\Request;

class CustomerRequest extends Request {

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
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'billing_address' => 'required',
                    'zip_code_id' => 'required|numeric|exists:zipcodes,id',
                    'phone_no' => 'required',
                    'credit_amount' => 'required|numeric',
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                $order = $this->route('order');

                return [
                    'po_number'     => 'required|alpha_num|max:50|unique:orders,po_number,'.$order->id,
                    'order_date'    => 'required|date',
                    'pickup_date'   => 'required|date',
                    'items'         => 'array',
                    'total_amount'  => 'required|numeric'
                ];
            }
            default:break;
        }
	}

}
