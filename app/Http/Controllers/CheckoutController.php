<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutShippingRequest;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;

/**
 * Controller for handling the checkout process.
 * 
 * This controller manages the checkout flow including
 * displaying the checkout form and processing shipping information.
 */
class CheckoutController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param CartService $cartService The cart service for managing cart operations
     */
    public function __construct(
        protected CartService $cartService
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
}
