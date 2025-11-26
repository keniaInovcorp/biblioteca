<?php

namespace App\Services;

use App\Models\Order;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

/**
 * Service for handling Stripe payment operations.
 *
 * This service manages payment intent creation and retrieval
 * via the Stripe API.
 */
class StripeService
{
    /**
     * Create a new StripeService instance.
     * Initializes the Stripe API with the secret key.
     */
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Create a Stripe PaymentIntent for the given order.
     *
     * @param Order $order The order to create a payment for
     * @return PaymentIntent The created Stripe PaymentIntent
     * @throws \Exception If the Stripe API call fails
     */
    public function createPaymentIntent(Order $order): PaymentIntent
    {
        try {
            return PaymentIntent::create([
                'amount' => (int)($order->total * 100),
                'currency' => 'eur',
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);
        } catch (ApiErrorException $e) {
            throw new \Exception('Erro ao criar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a Stripe PaymentIntent by ID.
     *
     * @param string $paymentIntentId The Stripe PaymentIntent ID
     * @return PaymentIntent The retrieved Stripe PaymentIntent
     * @throws \Exception If the Stripe API call fails
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            throw new \Exception('Erro ao recuperar pagamento: ' . $e->getMessage());
        }
    }
}
