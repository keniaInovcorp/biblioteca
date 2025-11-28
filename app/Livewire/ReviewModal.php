<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Review;
use App\Models\Submission;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use App\Events\ReviewCreated;
use Livewire\Attributes\On;

class ReviewModal extends Component
{
    public Book $book;
    public bool $showModal = false;
    public bool $isEditing = false;
    
    public string $comment = '';
    public int $rating = 5;
    
    public ?Review $currentReview = null;

    protected $rules = [
        'comment' => 'required|string|max:1000',
        'rating' => 'required|integer|min:1|max:5',
    ];

    protected $messages = [
        'comment.required' => 'O comentário é obrigatório.',
        'comment.max' => 'O comentário não pode ter mais de 1000 caracteres.',
        'rating.required' => 'A avaliação é obrigatória.',
        'rating.min' => 'A avaliação deve ser no mínimo 1.',
        'rating.max' => 'A avaliação deve ser no máximo 5.',
    ];

    public function mount(Book $book)
    {
        $this->book = $book;
        $this->loadCurrentReview();
    }

    protected function getUser(): ?\App\Models\User
    {
        /** @var \App\Models\User|null */
        return Auth::user();
    }

    protected function loadCurrentReview(): void
    {
        $user = $this->getUser();
        if (!$user) {
            $this->currentReview = null;
            return;
        }

        // Get the user's review for this book (any status)
        $this->currentReview = Review::where('user_id', $user->id)
            ->where('book_id', $this->book->id)
            ->first();

        if ($this->currentReview) {
            $this->comment = $this->currentReview->comment;
            $this->rating = $this->currentReview->rating;
        } else {
            $this->comment = '';
            $this->rating = 5;
        }
    }

    /**
     * Check if user can access the review modal.
     * User can access if:
     * - Has returned the book AND (no review OR review approved/rejected)
     * - Has a pending review (to view/edit/delete)
     */
    public function canAccessModal(): bool
    {
        $user = $this->getUser();
        if (!$user || !$user->hasRole('citizen')) {
            return false;
        }

        // Has returned the book?
        $hasReturnedBook = Submission::where('user_id', $user->id)
            ->where('book_id', $this->book->id)
            ->where('status', 'returned')
            ->exists();

        return $hasReturnedBook;
    }

    /**
     * Check if user can create a new review.
     */
    public function canCreateReview(): bool
    {
        $user = $this->getUser();
        if (!$user) {
            return false;
        }

        return $user->can('canReviewBook', $this->book->id);
    }

    #[On('openReviewModal')]
    public function openModal()
    {
        $this->loadCurrentReview();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->isEditing = false;
        $this->resetValidation();
        $this->loadCurrentReview(); // Reset form to current review state
    }

    public function startEditing()
    {
        if ($this->currentReview && $this->currentReview->isPending()) {
            $this->isEditing = true;
            $this->comment = $this->currentReview->comment;
            $this->rating = $this->currentReview->rating;
        }
    }

    public function cancelEditing()
    {
        $this->isEditing = false;
        if ($this->currentReview) {
            $this->comment = $this->currentReview->comment;
            $this->rating = $this->currentReview->rating;
        }
        $this->resetValidation();
    }

    public function submit()
    {
        $user = $this->getUser();
        if (!$user) {
            session()->flash('modal_error', 'Faça login para continuar.');
            return;
        }

        $this->validate();

        DB::beginTransaction();

        try {
            if ($this->currentReview && $this->isEditing) {
                // Update existing pending review
                if (!$user->can('update', $this->currentReview)) {
                    session()->flash('modal_error', 'Não tem permissão para editar esta review.');
                    DB::rollBack();
                    return;
                }

                $this->currentReview->update([
                    'comment' => $this->comment,
                    'rating' => $this->rating,
                ]);

                DB::commit();
                $this->isEditing = false;
                $this->loadCurrentReview();
                session()->flash('modal_success', 'Review atualizada com sucesso!');
            } else {
                // Create new review - only if no review exists
                if (!$user->can('canReviewBook', $this->book->id)) {
                    session()->flash('modal_error', 'Não pode criar uma nova review neste momento.');
                    DB::rollBack();
                    return;
                }

                $submission = Submission::where('user_id', $user->id)
                    ->where('book_id', $this->book->id)
                    ->where('status', 'returned')
                    ->latest()
                    ->first();

                $review = Review::create([
                    'user_id' => $user->id,
                    'book_id' => $this->book->id,
                    'submission_id' => $submission?->id,
                    'comment' => $this->comment,
                    'rating' => $this->rating,
                    'status' => 'pending',
                ]);

                Event::dispatch(new ReviewCreated($review));

                DB::commit();
                $this->loadCurrentReview();
                session()->flash('modal_success', 'Review enviada com sucesso! Aguarde aprovação do administrador.');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            session()->flash('modal_error', 'Ocorreu um erro. Tente novamente mais tarde.');
        }
    }

    public function deleteReview()
    {
        $user = $this->getUser();
        if (!$user || !$this->currentReview) {
            return;
        }

        if (!$user->can('delete', $this->currentReview)) {
            session()->flash('modal_error', 'Não tem permissão para apagar esta review.');
            return;
        }

        DB::beginTransaction();

        try {
            $this->currentReview->delete();
            DB::commit();

            $this->currentReview = null;
            $this->comment = '';
            $this->rating = 5;
            $this->isEditing = false;

            session()->flash('modal_success', 'Review removida com sucesso!');
            $this->loadCurrentReview();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            session()->flash('modal_error', 'Ocorreu um erro ao remover a review.');
        }
    }

    public function setRating(int $rating)
    {
        $this->rating = $rating;
    }

    public function render()
    {
        return view('livewire.review-modal', [
            'canAccess' => $this->canAccessModal(),
            'canCreate' => $this->canCreateReview(),
        ]);
    }
}

