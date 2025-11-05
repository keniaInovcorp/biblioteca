<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => [
                'required',
                'exists:books,id',
                function ($attribute, $value, $fail) {
                    $book = Book::find($value);
                    if ($book && !$book->isAvailable()) {
                        $fail('Este livro não está disponível para requisição.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => 'O livro é obrigatório.',
            'book_id.exists' => 'O livro selecionado não existe.',
        ];
    }
}
