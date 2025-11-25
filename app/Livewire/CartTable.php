<?php

namespace App\Livewire;

use App\Models\Book;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class CartTable extends Component
{
    /**
     * Success message to display to the user
     *
     * @var string
     */
    public string $successMessage = '';

    /**
     * Error message to display to the user
     *
     * @var string
     */
    public string $errorMessage = '';

    /**
     * Initialize the component
     *
     * @return void
     */
    public function mount(): void
    {
        //
    }

    /**
     * Add a book to the cart
     *
     * @param Book $book The book to add
     * @param int $quantity The quantity to add (default: 1)
     * @return void
     */
    public function addItem(Book $book, int $quantity = 1): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';

        $user = Auth::user();

        // Check if the user has permission to add items to the cart.
        if (!Gate::forUser($user)->allows('addItem', \App\Models\Cart::class)) {
            $this->errorMessage = 'Apenas cidadãos podem adicionar itens ao carrinho.';
            return;
        }

        if ($quantity < 1 || $quantity > 10) {
            $this->errorMessage = 'Quantidade inválida. Deve ser entre 1 e 10.';
            return;
        }

        app(CartService::class)->addItem($user, $book, $quantity);
        $this->successMessage = 'Livro adicionado ao carrinho!';

        // Trigger event to update counter in menu
        $this->dispatch('cart-updated');
    }

    /**
     * Update the quantity of a book in the cart
     *
     * @param int $bookId The ID of the book to update
     * @param int|string $quantity The new quantity (can be string from input)
     * @return void
     */
    public function updateQuantity(int $bookId, int|string $quantity): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';

        $user = Auth::user();
        $cart = app(CartService::class)->getCart($user);

        // Check if the user has permission to update the cart.
        if (!$cart || !Gate::forUser($user)->allows('updateItem', $cart)) {
            $this->errorMessage = 'Apenas cidadãos podem atualizar o carrinho.';
            return;
        }

        $quantity = (int) $quantity;

        if ($quantity < 1 || $quantity > 10) {
            $this->errorMessage = 'Quantidade inválida. Deve ser entre 1 e 10.';
            return;
        }

        $book = Book::findOrFail($bookId);
        app(CartService::class)->updateItemQuantity($user, $book, $quantity);
        $this->successMessage = 'Carrinho atualizado!';
        $this->dispatch('cart-updated');
    }

    /**
     * Remove a book from the cart
     *
     * @param int $bookId The ID of the book to remove
     * @return void
     */
    public function removeItem(int $bookId): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';

        $user = Auth::user();
        $cart = app(CartService::class)->getCart($user);

        // Check if the user has permission to remove items from the cart.
        if (!$cart || !Gate::forUser($user)->allows('removeItem', $cart)) {
            $this->errorMessage = 'Apenas cidadãos podem remover itens do carrinho.';
            return;
        }

        $book = Book::findOrFail($bookId);
        app(CartService::class)->removeItem($user, $book);
        $this->successMessage = 'Livro removido do carrinho!';
        $this->dispatch('cart-updated');
    }

    /**
     * Clear all items from the cart
     *
     * @return void
     */
    public function clearCart(): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';

        $user = Auth::user();
        $cart = app(CartService::class)->getCart($user);

        // Check if the user has permission to clear the cart.
        if (!$cart || !Gate::forUser($user)->allows('clearCart', $cart)) {
            $this->errorMessage = 'Apenas cidadãos podem limpar o carrinho.';
            return;
        }

        app(CartService::class)->clearCart($user);
        $this->successMessage = 'Carrinho limpo!';
        $this->dispatch('cart-updated');
    }

    /**
     * Render the component
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        $user = Auth::user();

        //Check if the user has permission to view the shopping cart.
        Gate::authorize('viewAny', \App\Models\Cart::class);

        $cart = app(CartService::class)->getCart($user);

        return view('livewire.cart-table', [
            'cart' => $cart,
        ]);
    }
}
