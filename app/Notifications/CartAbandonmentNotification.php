<?php

namespace App\Notifications;

use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CartAbandonmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Cart $cart The abandoned cart instance.
     */
    public function __construct(public Cart $cart)
    {
        $this->cart->loadMissing(['items.book']);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  object  $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  object  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('cart.index');

        return (new MailMessage)
            ->subject('Você esqueceu algo no seu carrinho?')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Notamos que você adicionou alguns livros ao seu carrinho, mas ainda não finalizou a compra.')
            ->line('Não perca a oportunidade de adquirir esses livros!')
            ->action('Finalizar Compra', $url)
            ->line('Se você precisar de ajuda ou tiver alguma dúvida, estamos aqui para ajudar!')
            ->salutation('Obrigada, Biblioteca');
    }
}

