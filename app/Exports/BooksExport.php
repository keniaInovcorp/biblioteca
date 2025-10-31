<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BooksExport implements FromCollection, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return [
            'ISBN',
            'Nome',
            'Editora',
            'Autores',
            'Bibliografia',
            'Imagem da Capa',
            'Preço',
        ];
    }

    public function map($book): array
    {
        $coverUrl = $book->cover_image_url 
            ? url($book->cover_image_url) 
            : 'Sem imagem';

        return [
            $book->isbn,
            $book->name,
            optional($book->publisher)->name ?? '',
            $book->authors->pluck('name')->join(', ') ?: '',
            $book->bibliography ?? '',
            $coverUrl,
            $book->price ?? '',
        ];
    }
}

