<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\BookAvailabilityAlert;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BookAvailabilityAlertButton extends Component
{
    public Book $book;
    public bool $hasAlert = false;

    public function mount(Book $book)
    {
        $this->book = $book;
        $this->checkAlert();
    }

    public function toggleAlert()
    {
        if (!Auth::check()) {
            return $this->redirect(route('login'));
        }

        // Check if an alert already exists (regardless of notified status)
        $alert = BookAvailabilityAlert::where('user_id', Auth::id())
            ->where('book_id', $this->book->id)
            ->first();

        if ($alert) {
            if ($alert->notified) {
                // Re-activate a previously notified alert
                $alert->update([
                    'notified' => false,
                    'notified_at' => null,
                ]);
                $this->hasAlert = true;
                session()->flash('success', 'Alerta reativado! Você será notificado quando este livro estiver disponível.');
            } else {
                // Remove active alert
                $alert->delete();
                $this->hasAlert = false;
                session()->flash('success', 'Alerta removido com sucesso!');
            }
        } else {
            // Create new alert
            if (!$this->book->isAvailable()) {
                BookAvailabilityAlert::create([
                    'user_id' => Auth::id(),
                    'book_id' => $this->book->id,
                    'notified' => false,
                ]);

                $this->hasAlert = true;
                session()->flash('success', 'Você será notificado quando este livro estiver disponível!');
            }
        }
    }

    protected function checkAlert()
    {
        if (Auth::check()) {
            $this->hasAlert = BookAvailabilityAlert::where('user_id', Auth::id())
                ->where('book_id', $this->book->id)
                ->where('notified', false)
                ->exists();
        }
    }

    public function render()
    {
        return view('livewire.book-availability-alert-button');
    }
}
