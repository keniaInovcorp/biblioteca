<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookId = $this->route('book')?->id ?? null;

        return [
            'isbn' => [
                'required',
                'string',
                'regex:/^\d{13}$/',
                Rule::unique('books', 'isbn')->ignore($bookId),
            ],
            'name' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'bibliography' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,png|max:2048',
            'price' => 'nullable|numeric|min:0|max:999999.99',
            'authors' => 'nullable|array',
            'authors.*' => 'exists:authors,id',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'isbn.regex' => 'O ISBN deve conter exatamente 13 dígitos numéricos.',
        ];
    }
}
