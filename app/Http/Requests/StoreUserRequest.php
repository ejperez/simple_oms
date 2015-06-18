<?php namespace SimpleOMS\Http\Requests;

class StoreUserRequest extends Request {

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
                    'name' => 'required|max:255|unique:users,name',
                    'email' => 'required|email|max:255|unique:users',
                    'role_id' => 'required|exists:roles,id',
                    'customer.first_name' => 'required',
                    'customer.last_name' => 'required',
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                $user = $this->route('user');

                return [
                    'name' => 'required|max:255|unique:users,name,'.$user->id,
                    'email' => 'required|email|max:255|unique:users,email,'.$user->id,
                    'current_password' => 'required|min:6',
                    'password' => 'sometimes|confirmed|min:6',
                    'customer.first_name' => 'required',
                    'customer.last_name' => 'required',
                    'role_id' => 'sometimes|required|exists:roles,id',
                ];
            }
            default:break;
        }
	}

}
