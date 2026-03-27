<?php
namespace App\Http\Controllers;
use App\Models\Event;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function show(string $slug)
    {
        $event = Event::with(['ticketTypes' => fn($q) => $q->where('status', 'active')])
            ->where('slug', $slug)->where('status', 'published')->firstOrFail();
        return view('events.checkout', compact('event'));
    }

    public function store(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->where('status', 'published')->firstOrFail();

        $request->validate([
            'items'                       => 'required|array',
            'items.*.ticket_type_id'      => 'required|exists:ticket_types,id',
            'items.*.quantity'            => 'required|integer|min:1',
        ]);

        $items = collect($request->items)->filter(fn($i) => ($i['quantity'] ?? 0) > 0)->values()->toArray();
        if (empty($items)) return back()->with('error', 'Selecciona al menos una entrada.');

        try {
            $order = $this->orderService->createOrder(Auth::user(), $event, $items);
            return redirect()->route('payment.checkout', $order)->with('success', 'Orden creada. Proceder al pago.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
