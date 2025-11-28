<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller for managing orders.
 *
 * Handles listing and displaying order details for both
 * administrators and regular users.
 */
class OrderController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of orders.
     *
     * Shows all orders for administrators, or only the
     * authenticated user's orders for regular users.
     *
     * @return View The orders index view
     */
    public function index(): View
    {
        return view('orders.index');
    }

    /**
     * Display the specified order.
     *
     * Shows detailed information about a specific order.
     * Users can only view their own orders, while administrators
     * can view any order.
     *
     * @param Order $order The order to display
     * @return View The order detail view
     */
    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['items.book', 'user']);

        return view('orders.show', compact('order'));
    }

    /**
     * Mark order as shipped.
     *
     * Only allowed if payment is paid and status is processing.
     * Changes status from 'processing' to 'shipped'.
     *
     * @param Order $order The order to mark as shipped
     * @return RedirectResponse Redirect back with success message
     */
    public function markAsShipped(Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        if ($order->payment_status !== Order::PAYMENT_STATUS_PAID) {
            return redirect()->back()->with('error', 'Apenas encomendas pagas podem ser marcadas como enviadas.');
        }

        if ($order->status !== Order::STATUS_PROCESSING) {
            return redirect()->back()->with('error', 'Apenas encomendas em processamento podem ser marcadas como enviadas.');
        }

        $order->update(['status' => Order::STATUS_SHIPPED]);

        return redirect()->back()->with('success', 'Encomenda marcada como enviada com sucesso.');
    }

    /**
     * Cancel an order.
     *
     * Only allowed if payment is pending and status is pending.
     * Changes status from 'pending' to 'cancelled'.
     *
     * @param Order $order The order to cancel
     * @return RedirectResponse Redirect back with success message
     */
    public function cancel(Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        if ($order->payment_status !== Order::PAYMENT_STATUS_PENDING) {
            return redirect()->back()->with('error', 'Apenas encomendas com pagamento pendente podem ser canceladas.');
        }

        if ($order->status !== Order::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Apenas encomendas pendentes podem ser canceladas.');
        }

        $order->update(['status' => Order::STATUS_CANCELLED]);

        return redirect()->back()->with('success', 'Encomenda cancelada com sucesso.');
    }
}
