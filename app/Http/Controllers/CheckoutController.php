<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutShippingRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Controller for handling the checkout process.
 * 
 * This controller manages the checkout flow including
 * displaying the checkout form, processing shipping information,
 * and handling Stripe payments.
 */
class CheckoutController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param CartService $cartService The cart service for managing cart operations
     * @param StripeService $stripeService The Stripe service for payment processing
     */
    public function __construct(
        protected CartService $cartService,
        protected StripeService $stripeService
    ) {}

    /**
     * Display the checkout page.
     * 
     * Retrieves the user's cart and displays the checkout form.
     * Redirects to cart if the cart is empty.
     *
     * @return View|RedirectResponse The checkout view or redirect to cart
     */
    public function index(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cart = $this->cartService->getCart($user);

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Seu carrinho está vazio!');
        }

        return view('checkout.index', compact('cart'));
    }

    /**
     * Process the shipping information and proceed to payment.
     * 
     * Validates the shipping data, stores it in the session,
     * and redirects to the payment page.
     * Redirects to cart if the cart is empty.
     *
     * @param CheckoutShippingRequest $request The validated shipping request
     * @return RedirectResponse Redirect to payment page or cart
     */
    public function store(CheckoutShippingRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cart = $this->cartService->getCart($user);

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Seu carrinho está vazio!');
        }

        session(['checkout_shipping' => $request->validated()]);

        return redirect()->route('checkout.payment');
    }

    /**
     * Display the payment page with Stripe integration.
     * 
     * Creates an order from the cart and shipping data,
     * then creates a Stripe PaymentIntent for processing.
     *
     * @return View|RedirectResponse The payment view or redirect on error
     */
    public function payment(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cart = $this->cartService->getCart($user);

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Seu carrinho está vazio!');
        }

        $shipping = session('checkout_shipping');

        if (!$shipping) {
            return redirect()->route('checkout.index')
                ->with('error', 'Por favor, preencha os dados de entrega primeiro.');
        }

        $order = DB::transaction(function () use ($user, $cart, $shipping) {
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'total' => $cart->total,
                'shipping_name' => $shipping['shipping_name'],
                'shipping_email' => $shipping['shipping_email'],
                'shipping_phone' => $shipping['shipping_phone'] ?? null,
                'shipping_address_line_1' => $shipping['shipping_address_line_1'],
                'shipping_address_line_2' => $shipping['shipping_address_line_2'] ?? null,
                'shipping_city' => $shipping['shipping_city'],
                'shipping_postal_code' => $shipping['shipping_postal_code'],
                'shipping_country' => $shipping['shipping_country'],
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'book_id' => $item->book_id,
                    'quantity' => $item->quantity,
                    'price' => $item->book->price,
                    'book_name' => $item->book->name,
                ]);
            }

            return $order;
        });

        try {
            $paymentIntent = $this->stripeService->createPaymentIntent($order);

            $order->update(['stripe_payment_intent_id' => $paymentIntent->id]);

            return view('checkout.payment', [
                'order' => $order,
                'paymentIntent' => $paymentIntent,
                'stripeKey' => config('stripe.key'),
            ]);
        } catch (\Exception $e) {
            return redirect()->route('checkout.index')
                ->with('error', 'Erro ao processar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Display the order success page.
     * 
     * Shows the order confirmation after successful payment.
     * Clears the cart and shipping session data.
     *
     * @param Order $order The completed order
     * @return View The success view
     */
    public function success(Order $order): View
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->cartService->clearCart($user);
        session()->forget('checkout_shipping');

        return view('checkout.success', compact('order'));
    }
}
