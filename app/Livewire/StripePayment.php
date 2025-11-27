<?php

namespace App\Livewire;

use App\Models\Order;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Livewire component for handling Stripe payment.
 *
 * Integrates with Stripe.js via Alpine.js for secure card processing.
 */
class StripePayment extends Component
{
    /**
     * The order being paid.
     */
    #[Locked]
    public Order $order;

    /**
     * The Stripe PaymentIntent client secret.
     */
    #[Locked]
    public string $clientSecret;

    /**
     * The Stripe publishable key.
     */
    #[Locked]
    public string $stripeKey;

    /**
     * Error message from payment processing.
     */
    public string $errorMessage = '';

    /**
     * Whether the payment is being processed.
     */
    public bool $processing = false;

    /**
     * Initialize the component.
     */
    public function mount(Order $order, string $clientSecret, string $stripeKey): void
    {
        $this->order = $order;
        $this->clientSecret = $clientSecret;
        $this->stripeKey = $stripeKey;
    }

    /**
     * Set error message.
     */
    public function setError(string $message): void
    {
        $this->errorMessage = $message;
        $this->processing = false;
    }

    /**
     * Handle successful payment - redirect to success page.
     */
    public function paymentSucceeded(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Clear cart and session
        app(CartService::class)->clearCart($user);
        session()->forget('checkout_shipping');

        // Update order status
        $this->order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        $this->redirect(route('checkout.success', $this->order), navigate: true);
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.stripe-payment');
    }
}
