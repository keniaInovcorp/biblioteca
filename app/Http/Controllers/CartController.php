<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CartController extends Controller
{
    /**
     * Display the cart page
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        // Verify that the user has permission to access the shopping cart.
        Gate::authorize('viewAny', Cart::class);

        return view('cart.index');
    }
}
