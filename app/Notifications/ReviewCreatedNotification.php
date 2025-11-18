<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ReviewCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Review $review;

    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Carry relationships
        $this->review->loadMissing(['user', 'book']);

        // URL to the reviews page at books.show
        $url = 'http://127.0.0.1:8000/books/' . $this->review->book->id . '#reviews';

        return (new MailMessage)
            ->subject('Nova Review Pendente de Moderação')
            ->greeting('Olá!')
            ->line('Uma nova review foi criada e está aguardando moderação.')
            ->line("**Cidadão:** {$this->review->user->name}")
            ->line("**Email:** {$this->review->user->email}")
            ->line("**Livro:** {$this->review->book->name}")
            ->line("**Avaliação:** {$this->review->rating}/5 estrelas")
            ->line("**Comentário:** " . Str::limit($this->review->comment, 100))
            ->line('Por favor, revise e modere esta review.')
            ->action('Ver Review', $url)
            ->salutation('Obrigada');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
