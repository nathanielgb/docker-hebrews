<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
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
            'menu' => ['required', 'max:255', Rule::unique('menus', 'name')->ignore($this->menu_id)],
            'unit' => 'required|numeric',
            'regular_price' => 'nullable|numeric|between:0,999999.99',
            'retail_price' => 'nullable|numeric|between:0,999999.99',
            'wholesale_price' => 'nullable|numeric|between:0,999999.99',
            'distributor_price' => 'nullable|numeric|between:0,999999.99',
            'rebranding_price' => 'nullable|numeric|between:0,999999.99',
            'category' => ['required', Rule::exists('menu_categories', 'id')],
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
            'dinein_price' => 'dine-in price',
            'takeout_price' => 'take-out price',
        ];
    }
}
