<?php namespace SimpleOMS\Http\Requests;

use SimpleOMS\Http\Requests\Request;

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
		return [
			'po_number' => 'required|alpha_num|unique:orders|max:50',
            'order_date' => 'required|date',
            'pickup_date' => 'required|date',
            'items' => 'array',
            'total_amount' => 'required|numeric'
		];
	}
}
