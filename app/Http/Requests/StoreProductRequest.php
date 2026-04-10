<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_name' => 'required',
            'product_desc' => 'nullable',
            'product_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category_id' => 'required',
            'price'=> 'required',
            'quantity'=> 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'product_name.required' => 'Product name require',
            'category_id.required'=>'Category needed',
            'price.required'=>'Please fill price',
            'quantity.required'=>'Enter quantity'
        ];

    }
}
