<?php

namespace App\Notifications;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookAvailabilityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Book $book;

    /**
     * Create a new notification instance.
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
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
        // Load relationships
        $this->book->loadMissing(['publisher', 'authors']);

        $url = route('books.show', $this->book);

        return (new MailMessage)
            ->subject('Livro Disponível: ' . $this->book->name)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('O livro que você estava aguardando está agora disponível para requisição.')
            ->line("**Livro:** {$this->book->name}")
            ->line("**ISBN:** {$this->book->isbn}")
            ->when($this->book->publisher, function ($mail) {
                return $mail->line("**Editora:** {$this->book->publisher->name}");
            })
            ->when($this->book->authors->isNotEmpty(), function ($mail) {
                $authors = $this->book->authors->pluck('name')->join(', ');
                return $mail->line("**Autores:** {$authors}");
            })
            ->line('Aproveite para requisitar este livro agora!')
            ->action('Ver Livro', $url)
            ->salutation('Obrigada, Biblioteca');
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
