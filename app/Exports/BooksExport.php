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
            'Nome',
            'ISBN',
            'Editora',
            'Autores',
            'Preço',
            'Criado em',
        ];
    }

    public function map($book): array
    {
        return [
            $book->name,
            $book->isbn,
            optional($book->publisher)->name,
            $book->authors->pluck('name')->join(', '),
            $book->price,
            optional($book->created_at)->format('d/m/Y H:i'),
        ];
    }
}

