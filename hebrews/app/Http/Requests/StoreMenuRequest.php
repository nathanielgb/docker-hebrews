<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
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
        'menu' => ['required', 'max:255', Rule::unique('menus', 'name')->where(function ($query) {
            $query->where('branch_id', $this->branch);
        })],
        'code' => ['required', 'max:255', 'alpha_dash', Rule::unique('menus', 'code')->where(function ($query) {
            $query->where('branch_id', $this->branch);
        })],
        'unit' => 'required|numeric|min:1',
        'regular_price' => 'nullable|numeric|between:0,999999.99',
        'retail_price' => 'nullable|numeric|between:0,999999.99',
        'wholesale_price' => 'nullable|numeric|between:0,999999.99',
        'distributor_price' => 'nullable|numeric|between:0,999999.99',
        'rebranding_price' => 'nullable|numeric|between:0,999999.99',
        'category' => ['required', Rule::exists('menu_categories', 'id')],
        'sub_category' => ['nullable'],
        'branch' => ['required', Rule::exists('branches', 'id')],
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
