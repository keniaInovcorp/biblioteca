<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

/**
 * Controller for handling Stripe webhook events.
 * 
 * This controller processes webhook events sent by Stripe to notify
 * the application about payment status changes.
 */
class StripeWebhookController extends Controller
{
    /**
     * Handle incoming Stripe webhook requests.
     * 
     * Validates the webhook signature and processes the event.
     * Returns JSON responses as required by Stripe's webhook API.
     *
     * @param Request $request The HTTP request containing the webhook payload
     * @return \Illuminate\Http\JsonResponse JSON response indicating success or error
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
        }

        return response()->json(['received' => true]);
    }

    /**
     * Handle successful payment event.
     * 
     * Updates the order status to 'paid' and 'processing' when
     * a payment intent succeeds.
     *
     * @param object $paymentIntent The Stripe PaymentIntent object
     * @return void
     */
    protected function handlePaymentSucceeded($paymentIntent): void
    {
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);
        }
    }

    /**
     * Handle failed payment event.
     * 
     * Updates the order payment status to 'failed' when
     * a payment intent fails.
     *
     * @param object $paymentIntent The Stripe PaymentIntent object
     * @return void
     */
    protected function handlePaymentFailed($paymentIntent): void
    {
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            $order->update(['payment_status' => 'failed']);
        }
    }
}

