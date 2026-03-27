<?php
namespace App\Services;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(private TicketService $ticketService) {}

    public function createOrder(User $user, Event $event, array $items): Order
    {
        $subtotal = 0;
        $orderItems = [];

        foreach ($items as $item) {
            $ticketType = TicketType::findOrFail($item['ticket_type_id']);
            if ($ticketType->event_id !== $event->id) {
                throw new \InvalidArgumentException('Ticket type does not belong to this event.');
            }
            if ($ticketType->available < $item['quantity']) {
                throw new \InvalidArgumentException('Not enough tickets available for: ' . $ticketType->name);
            }
            $lineSubtotal = $ticketType->price * $item['quantity'];
            $subtotal += $lineSubtotal;
            $orderItems[] = [
                'ticket_type_id' => $ticketType->id,
                'quantity'       => $item['quantity'],
                'unit_price'     => $ticketType->price,
                'subtotal'       => $lineSubtotal,
            ];
        }

        $fee = round($subtotal * 0.05, 2);
        $total = $subtotal + $fee;

        $order = Order::create([
            'user_id'        => $user->id,
            'event_id'       => $event->id,
            'order_number'   => 'RCT-' . strtoupper(Str::random(8)),
            'subtotal'       => $subtotal,
            'fee'            => $fee,
            'total'          => $total,
            'status'         => 'pending',
            'payment_method' => 'mercadopago',
        ]);

        foreach ($orderItems as $item) {
            $order->orderItems()->create($item);
        }

        return $order;
    }

    public function approveOrder(Order $order): void
    {
        $order->update(['status' => 'approved']);
        $this->ticketService->generateTickets($order);
    }

    public function cancelOrder(Order $order): void
    {
        $order->update(['status' => 'cancelled']);
    }
}
