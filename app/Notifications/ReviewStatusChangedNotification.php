<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ReviewStatusChangedNotification extends Notification implements ShouldQueue
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
       $url = route('books.show', $this->review->book->id);

        $message = (new MailMessage)
            ->subject('Status da sua Review foi Alterado')
            ->greeting('Olá ' . $notifiable->name . '!');

        if ($this->review->isActive()) {
            $message->line('Sua review do livro **' . $this->review->book->name . '** foi **aprovada** e está agora visível para outros cidadãos.')
                ->action('Ver Livro', $url)
                ->line('Obrigado por compartilhar sua opinião!');
        } else {
            $message->line('Sua review do livro **' . $this->review->book->name . '** foi **rejeitada**.')
                ->line('**Justificativa:** ' . $this->review->rejection_reason)
                ->action('Ver Livro', $url)
                ->salutation('Obrigada');
        }

        return $message;
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
