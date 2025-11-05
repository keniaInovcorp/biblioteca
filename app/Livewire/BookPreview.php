<?php

namespace App\Livewire;

use App\Models\Book;
use Livewire\Component;
use Livewire\Attributes\On;

class BookPreview extends Component
{
    public $bookId = null;

    #[On('book-selected')]
    public function updateBookId($bookId)
    {
        $this->bookId = !empty($bookId['bookId']) ? (int) $bookId['bookId'] : null;
    }

    public function getBookProperty()
    {
        if (!$this->bookId) {
            return null;
        }

        return Book::with('publisher', 'authors')
            ->find($this->bookId);
    }

    public function render()
    {
        return view('livewire.book-preview');
    }
}
