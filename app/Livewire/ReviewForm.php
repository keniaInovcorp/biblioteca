<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Review;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use App\Events\ReviewCreated;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReviewForm extends Component
{
     use AuthorizesRequests;
    public Book $book;
    public ?Review $review = null;
    public string $comment = '';
    public int $rating = 5;
    public bool $isEditing = false;

    protected $rules = [
        'comment' => 'required|string|min:10|max:1000',
        'rating' => 'required|integer|min:1|max:5',
    ];

    protected $messages = [
        'comment.required' => 'O comentário é obrigatório.',
        'comment.min' => 'O comentário deve ter pelo menos 10 caracteres.',
        'comment.max' => 'O comentário não pode ter mais de 1000 caracteres.',
        'rating.required' => 'A avaliação é obrigatória.',
        'rating.min' => 'A avaliação deve ser no mínimo 1.',
        'rating.max' => 'A avaliação deve ser no máximo 5.',
    ];

    public function mount(Book $book)
    {
        $this->book = $book;
        $this->loadUserReview();
    }

    /**
     * Get the authenticated user or null.
     *
     * @return \App\Models\User|null
     */
    protected function getUser(): ?\App\Models\User
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user;
    }

    protected function loadUserReview(): void
    {
        $user = $this->getUser();

        if ($user) {
            $this->review = Review::where('user_id', $user->id)
                ->where('book_id', $this->book->id)
                ->first();

            if ($this->review) {
                $this->comment = $this->review->comment;
                $this->rating = $this->review->rating;
            }
        }
    }

    public function canReview(): bool
    {
        $user = $this->getUser();

        if (!$user) {
            return false;
        }

        return $user->can('canReviewBook', $this->book->id);
    }

    public function submit()
    {
        $user = $this->getUser();

        if (! $user) {
            abort(403, 'Ação não autorizada — faça login.');
        }

        $this->validate();

        DB::beginTransaction();

        try {
            if ($this->review && $this->isEditing) {
                // Atualizar review existente
                if (! $user->can('update', $this->review)) {
                    abort(403, 'Ação não autorizada.');
                }

                $this->review->update([
                    'comment' => $this->comment,
                    'rating'  => $this->rating,
                ]);

                DB::commit();
                $this->isEditing = false;
                $this->loadUserReview();
                session()->flash('success', 'Review atualizada com sucesso!');
            } else {
                // Criar nova review
                if (! $user->can('canReviewBook', $this->book->id)) {
                    abort(403, 'Ação não autorizada.');
                }

                $review = Review::create([
                    'user_id'       => $user->id,
                    'book_id'       => $this->book->id,
                    'submission_id' => $this->getLastReturnedSubmission($user->id),
                    'comment'       => $this->comment,
                    'rating'        => $this->rating,
                    'status'        => 'pending',
                ]);

                Event::dispatch(new ReviewCreated($review));

                DB::commit();
                $this->loadUserReview();
                session()->flash('success', 'Review enviado com sucesso! Aguarde aprovação do administrador.');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            session()->flash('error', 'Ocorreu um erro ao processar a review. Tente novamente mais tarde.');
        }
    }

    public function edit()
    {
        if ($this->review && $this->review->isPending()) {
            $this->isEditing = true;
        }
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->loadUserReview();
    }

    public function delete()
    {
        $user = $this->getUser();

        if (! $user || ! $this->review) {
            abort(403, 'Ação não autorizada.');
        }

        if (! $user->can('delete', $this->review)) {
            abort(403, 'Ação não autorizada.');
        }

        DB::beginTransaction();

        try {
            $this->review->delete();
            DB::commit();

            $this->review = null;
            $this->comment = '';
            $this->rating = 5;
            $this->isEditing = false;

            session()->flash('success', 'Review removida com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            session()->flash('error', 'Ocorreu um erro ao remover a review. Tente novamente mais tarde.');
        }
    }

    protected function getLastReturnedSubmission(int $userId): ?int
    {
        return \App\Models\Submission::where('user_id', $userId)
            ->where('book_id', $this->book->id)
            ->where('status', 'returned')
            ->latest()
            ->value('id');
    }

    public function render()
    {
        return view('livewire.review-form');
    }
}
