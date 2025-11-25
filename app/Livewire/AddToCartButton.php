<?php

namespace App\Livewire;

use App\Models\Book;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Livewire component for adding books to the shopping cart.
 * 
 * This component is displayed on the book detail page and allows
 * citizens to add books to their cart with a specified quantity.
 */
class AddToCartButton extends Component
{
    /**
     * The book to be added to the cart.
     *
     * @var Book
     */
    public Book $book;

    /**
     * The quantity of books to add to the cart.
     *
     * @var int
     */
    public int $quantity = 1;

    /**
     * Success message to display after adding to cart.
     *
     * @var string
     */
    public string $successMessage = '';

    /**
     * Initialize the component with the book.
     *
     * @param Book $book The book to be added to the cart
     * @return void
     */
    public function mount(Book $book): void
    {
        $this->book = $book;
    }

    /**
     * Add the book to the user's cart.
     * 
     * Only authenticated citizens can add items to the cart.
     * The quantity must be between 1 and 10.
     * Dispatches 'cart-updated' event on success.
     *
     * @return void
     */
    public function addToCart(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            $this->redirect(route('login'));
            return;
        }

        // Only citizens can add items to cart
        if (!$user->hasRole('citizen')) {
            return;
        }

        if ($this->quantity < 1 || $this->quantity > 10) {
            return;
        }

        app(CartService::class)->addItem($user, $this->book, $this->quantity);

        $this->successMessage = 'Livro adicionado ao carrinho!';
        $this->dispatch('cart-updated');
    }

    /**
     * Render the component view.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.add-to-cart-button');
    }
}
