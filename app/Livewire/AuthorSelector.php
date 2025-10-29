<?php

namespace App\Livewire;

use App\Models\Author;
use Livewire\Component;
use Illuminate\Support\Collection;

class AuthorSelector extends Component
{
    public $selectedAuthors = [];
    public $search = '';
    public $authors = [];
    public $showDropdown = false;

    public function mount($selectedAuthors = [])
    {
        $this->selectedAuthors = is_array($selectedAuthors) ? $selectedAuthors : [];
        // Não carregar todos os autores no mount - só quando necessário
    }

    public function openDropdown()
    {
        $this->showDropdown = true;
        // Sempre carregar autores quando abre (todos se não houver busca)
        $this->loadAuthors();
    }

    public function updatedShowDropdown($value)
    {
        if ($value) {
            // Quando abre o dropdown, carregar autores, todos se não houver busca.
            $this->loadAuthors();
        }
    }

    public function loadAuthors()
    {
        $query = Author::orderBy('name');
        
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        
        $this->authors = $query->get();
    }

    public function updatedSearch()
    {
        $this->loadAuthors();
        $this->showDropdown = true;
    }

    public function addAuthor($authorId)
    {
        if (!in_array($authorId, $this->selectedAuthors)) {
            $this->selectedAuthors[] = $authorId;
            $this->search = ''; 
            $this->showDropdown = false;
            $this->loadAuthors();
        }
    }

    public function removeAuthor($authorId)
    {
        $this->selectedAuthors = array_filter($this->selectedAuthors, function($id) use ($authorId) {
            return $id != $authorId;
        });
        $this->loadAuthors();
    }

    public function getSelectedAuthorsData()
    {
        if (empty($this->selectedAuthors)) {
            return collect();
        }
        return Author::whereIn('id', $this->selectedAuthors)->get();
    }

    public function render()
    {
        return view('livewire.author-selector', [
            'selectedAuthorsData' => $this->getSelectedAuthorsData(),
            'availableAuthors' => collect($this->authors)->filter(function($author) {
                return !in_array($author->id, $this->selectedAuthors);
            }),
        ]);
    }
}
