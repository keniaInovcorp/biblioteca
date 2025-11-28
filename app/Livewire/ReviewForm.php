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
    public ?int $reviewId = null;
    public string $comment = '';
    public int $rating = 5;
    public bool $isEditing = false;

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
        $this->loadUserReview();
    }

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
            $review = Review::where('user_id', $user->id)
                ->where('book_id', $this->book->id)
                ->first();

            if ($review) {
                $this->reviewId = $review->id;
                $this->comment = $review->comment;
                $this->rating = $review->rating;
            } else {
                $this->reviewId = null;
            }
        }
    }
    
    /**
     * Get the review model from ID.
     */
    protected function getReview(): ?Review
    {
        return $this->reviewId ? Review::find($this->reviewId) : null;
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
            $review = $this->getReview();
            
            if ($review && $this->isEditing) {
                if (! $user->can('update', $review)) {
                    abort(403, 'Ação não autorizada.');
                }

                $review->update([
                    'comment' => $this->comment,
                    'rating'  => $this->rating,
                ]);

                DB::commit();
                $this->isEditing = false;
                $this->loadUserReview();
                session()->flash('success', 'Review atualizada com sucesso!');
            } else {
                if (! $user->can('canReviewBook', $this->book->id)) {
                    abort(403, 'Você precisa ter devolvido este livro para criar uma review.');
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
                session()->flash('success', 'Review enviada com sucesso! Aguarde aprovação do administrador.');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            session()->flash('error', 'Ocorreu um erro ao processar a review. Tente novamente mais tarde.');
        }
    }

    public function edit()
    {
        $review = $this->getReview();
        
        if ($review && $review->isPending()) {
            $this->isEditing = true;
            $this->comment = $review->comment;
            $this->rating = $review->rating;
        }
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $review = $this->getReview();
        if ($review) {
            $this->comment = $review->comment;
            $this->rating = $review->rating;
        }
    }

    public function delete()
    {
        $user = $this->getUser();
        $review = $this->getReview();

        if (! $user || ! $review) {
            abort(403, 'Ação não autorizada.');
        }

        if (! $user->can('delete', $review)) {
            abort(403, 'Ação não autorizada.');
        }

        DB::beginTransaction();

        try {
            $review->delete();
            DB::commit();

            $this->reviewId = null;
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
        $review = $this->getReview();
        
        return view('livewire.review-form', [
            'review' => $review,
        ]);
    }
}
