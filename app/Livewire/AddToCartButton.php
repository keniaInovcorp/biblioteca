<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Livewire component for adding books to the shopping cart.
 *
 * This component is displayed on the book detail page and allows
 * citizens to add books to their cart with a specified quantity.
 */
class AddToCartButton extends Component
{
    use AuthorizesRequests;

    /**
     * The book to be added to the cart.
     * Locked to prevent tampering from the frontend.
     */
    #[Locked]
    public Book $book;

    /**
     * Whether to render inline without card wrapper.
     * Locked to prevent tampering from the frontend.
     */
    #[Locked]
    public bool $inline = false;

    /**
     * The quantity of books to add to the cart.
     */
    #[Validate('required|integer|min:1|max:10')]
    public int $quantity = 1;

    /**
     * Success message to display after adding to cart.
     */
    public string $successMessage = '';

    /**
     * Error message to display on failure.
     */
    public string $errorMessage = '';

    /**
     * Initialize the component with the book.
     *
     * @param Book $book The book to be added to the cart
     * @param bool $inline Whether to render inline without card wrapper
     */
    public function mount(Book $book, bool $inline = false): void
    {
        $this->book = $book;
        $this->inline = $inline;
    }

    /**
     * Add the book to the user's cart.
     *
     * Uses policy authorization and validation.
     * Dispatches 'cart-updated' event on success.
     *
     * @param CartService $cartService Injected cart service
     */
    public function addToCart(CartService $cartService): void
    {
        // Clear previous messages
        $this->successMessage = '';
        $this->errorMessage = '';

        // Check authentication
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            $this->redirect(route('login'));
            return;
        }

        // Authorize using Cart policy
        $this->authorize('addItem', Cart::class);

        // Validate quantity
        $this->validate();

        // Check book has price
        if (!$this->book->price) {
            $this->errorMessage = 'Este livro não está disponível para compra.';
            return;
        }

        try {
            $cartService->addItem($user, $this->book, $this->quantity);

            $this->successMessage = 'Livro adicionado ao carrinho!';
            $this->dispatch('cart-updated');

            // Reset quantity after successful add
            $this->quantity = 1;
        } catch (\Exception $e) {
            $this->errorMessage = 'Erro ao adicionar ao carrinho. Tente novamente.';
        }
    }

    /**
     * Check if the current user can add items to cart.
     */
    public function canAddToCart(): bool
    {
        $user = Auth::user();

        return $user
            && $user->can('addItem', Cart::class)
            && $this->book->price;
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('livewire.add-to-cart-button', [
            'canAdd' => $this->canAddToCart(),
        ]);
    }
}
