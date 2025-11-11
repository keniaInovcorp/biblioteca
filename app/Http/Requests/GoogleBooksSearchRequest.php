<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleBooksSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:title,isbn,raw',
            'q' => 'required_if:type,title,raw|string|min:2',
            'isbn' => 'required_if:type,isbn|string|min:10',
            'maxResults' => 'nullable|integer|min:1|max:40',
        ];
    }
}


