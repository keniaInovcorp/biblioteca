<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Form request for validating checkout shipping information.
 * 
 * This request validates all shipping address fields required
 * during the checkout process.
 */
class CheckoutShippingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * Only authenticated users can submit shipping information.
     *
     * @return bool True if user is authenticated
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * Validates shipping name, email, phone, address, city,
     * postal code, and country fields.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'nullable|string|max:20',
            'shipping_address_line_1' => 'required|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:100',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     * 
     * Returns Portuguese error messages for each validation rule.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'shipping_name.required' => 'O nome é obrigatório.',
            'shipping_email.required' => 'O email é obrigatório.',
            'shipping_email.email' => 'O email deve ser válido.',
            'shipping_address_line_1.required' => 'O endereço é obrigatório.',
            'shipping_city.required' => 'A cidade é obrigatória.',
            'shipping_postal_code.required' => 'O código postal é obrigatório.',
            'shipping_country.required' => 'O país é obrigatório.',
        ];
    }
}
