<?php
namespace App\Services;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketScan;
use App\Models\User;
use Illuminate\Support\Str;

class TicketService
{
    public function __construct(private QrCodeService $qrCodeService) {}

    public function generateTickets(Order $order): void
    {
        $order->load('orderItems.ticketType');
        foreach ($order->orderItems as $item) {
            for ($i = 0; $i < $item->quantity; $i++) {
                $code = strtoupper(Str::uuid()->toString());
                $qrPath = $this->qrCodeService->generate($code);
                Ticket::create([
                    'order_id'       => $order->id,
                    'order_item_id'  => $item->id,
                    'user_id'        => $order->user_id,
                    'event_id'       => $order->event_id,
                    'ticket_type_id' => $item->ticket_type_id,
                    'ticket_code'    => $code,
                    'qr_code_path'   => $qrPath,
                    'status'         => 'valid',
                ]);
            }
            $item->ticketType->increment('quantity_sold', $item->quantity);
        }
    }

    public function validateTicket(string $code, User $scanner): array
    {
        $ticket = Ticket::where('ticket_code', $code)->with('event')->first();

        if (!$ticket) {
            return ['result' => 'invalid', 'message' => 'Ticket no encontrado.'];
        }

        if ($ticket->status === 'used') {
            TicketScan::create([
                'ticket_id'  => $ticket->id,
                'scanned_by' => $scanner->id,
                'scanned_at' => now(),
                'result'     => 'already_used',
                'notes'      => 'Already checked in at ' . $ticket->checked_in_at,
            ]);
            return ['result' => 'already_used', 'message' => 'Ticket ya utilizado el ' . $ticket->checked_in_at?->format('d/m/Y H:i') . '.'];
        }

        if ($ticket->status === 'cancelled') {
            TicketScan::create([
                'ticket_id'  => $ticket->id,
                'scanned_by' => $scanner->id,
                'scanned_at' => now(),
                'result'     => 'cancelled',
            ]);
            return ['result' => 'cancelled', 'message' => 'Ticket cancelado.'];
        }

        $ticket->update(['status' => 'used', 'checked_in_at' => now()]);
        TicketScan::create([
            'ticket_id'  => $ticket->id,
            'scanned_by' => $scanner->id,
            'scanned_at' => now(),
            'result'     => 'valid',
        ]);
        return ['result' => 'valid', 'message' => 'Ticket válido. ¡Bienvenido!', 'ticket' => $ticket];
    }
}
