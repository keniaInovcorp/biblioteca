<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Notifications\CartAbandonmentNotification;
use Illuminate\Console\Command;

class SendCartAbandonmentNotifications extends Command
{
    protected $signature = 'cart:send-abandonment-notifications';
    protected $description = 'Send notifications to users who abandoned their cart';

    /**
     * Execute the console command.
     *
     * Finds carts that have items, were updated between 1 and 2 hours ago,
     * and haven't been notified in the last 24 hours.
     * Sends a notification to the user and caches the notification sent status.
     *
     * @return int
     */
    public function handle(): int
    {
        $oneHourAgo = now()->subHour();

        $carts = Cart::with(['user', 'items.book'])
            ->whereHas('items')
            ->where('updated_at', '<=', $oneHourAgo)
            ->where('updated_at', '>=', now()->subHours(2))
            ->get();

        $count = 0;

        foreach ($carts as $cart) {
            $cacheKey = "cart_abandonment_notified_{$cart->id}";

            if (!cache()->has($cacheKey)) {
                $cart->user->notify(new CartAbandonmentNotification($cart));
                cache()->put($cacheKey, true, now()->addHours(24));
                $count++;
            }
        }

        $this->info("Sent {$count} cart abandonment notifications.");

        return Command::SUCCESS;
    }
}

