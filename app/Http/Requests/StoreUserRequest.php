<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'name' => 'required',
            'email' => ['required','email',Rule::unique('users')->ignore($this->user)],
            'password' => 'required|min:6',
            'role' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is needed',
            'name'=> "Enter name",
            'password'=> "Fill the password",
            'role'=> "Role should not be blank"

        ];
    }
}
