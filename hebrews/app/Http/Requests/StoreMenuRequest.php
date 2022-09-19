<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
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
        'menu' => 'required|max:255',
        'unit' => 'required|numeric|min:1',
        'regular_price' => 'nullable|numeric|between:0,999999.99',
        'retail_price' => 'nullable|numeric|between:0,999999.99',
        'wholesale_price' => 'nullable|numeric|between:0,999999.99',
        'distributor_price' => 'nullable|numeric|between:0,999999.99',
        'rebranding_price' => 'nullable|numeric|between:0,999999.99',
        'category' => 'required',
        'sub_category' => 'nullable',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'menu' => 'name',
        ];
    }

}
