<?php
namespace App\Http\Controllers\Buyer;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()
            ->with('event')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('buyer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        $order->load(['event', 'orderItems.ticketType', 'tickets.ticketType', 'payment']);
        return view('buyer.orders.show', compact('order'));
    }
}
