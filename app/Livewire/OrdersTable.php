<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class OrdersTable extends Component
{
    use WithPagination;

    #[Url(as: 'page', except: 1)]
    public int $page = 1;

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Order::query();

        // If not admin, only show user's orders
        if (!$user->can('create', \App\Models\Book::class)) {
            $query->where('user_id', $user->id);
        }

        $orders = $query->with(['items.book', 'user'])
            ->latest()
            ->paginate(5);

        // Statistics, same query restrictions
        $statsQuery = Order::query();
        if (!$user->can('create', \App\Models\Book::class)) {
            $statsQuery->where('user_id', $user->id);
        }

        $stats = [
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'cancelled' => (clone $statsQuery)->where('status', 'cancelled')->count(),
            'paid' => (clone $statsQuery)->where('payment_status', 'paid')->where('status', 'processing')->count(),
            'shipped' => (clone $statsQuery)->where('status', 'shipped')->count(),
        ];

        return view('livewire.orders-table', [
            'orders' => $orders,
            'stats' => $stats,
        ]);
    }
}
