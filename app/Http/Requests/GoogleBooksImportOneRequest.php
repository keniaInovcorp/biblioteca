<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleBooksImportOneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required_without:isbn|string',
            'isbn' => 'required_without:id|string|min:10',
        ];
    }
}


