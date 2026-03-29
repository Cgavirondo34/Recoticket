<?php

namespace App\Http\Controllers\Api\Field;

use App\Http\Controllers\Controller;
use App\Models\FieldReservation;
use App\Models\FieldSlot;
use App\Services\Field\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(private readonly ReservationService $service) {}

    /**
     * GET /api/field/calendar
     * Returns reservations grouped by date for the given range.
     */
    public function calendar(Request $request): JsonResponse
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $tenantId = $request->user()->tenant_id;
        $calendar = $this->service->getCalendar($tenantId, $request->from, $request->to);

        return response()->json($calendar);
    }

    /** GET /api/field/reservations */
    public function index(Request $request): JsonResponse
    {
        $query = FieldReservation::with(['slot', 'member', 'paymentMethod'])
            ->when($request->date, fn($q, $d) => $q->whereDate('reservation_date', $d))
            ->when($request->from, fn($q, $d) => $q->where('reservation_date', '>=', $d))
            ->when($request->to, fn($q, $d) => $q->where('reservation_date', '<=', $d))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->slot_id, fn($q, $id) => $q->where('field_slot_id', $id))
            ->when($request->member_id, fn($q, $id) => $q->where('member_id', $id))
            ->orderBy('reservation_date')
            ->orderBy('start_time');

        return response()->json($query->paginate($request->per_page ?? 20));
    }

    /** POST /api/field/reservations */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'field_slot_id'    => 'required|integer|exists:field_slots,id',
            'reservation_date' => 'required|date',
            'member_id'        => 'nullable|integer|exists:members,id',
            'customer_name'    => 'nullable|string|max:255',
            'customer_phone'   => 'nullable|string|max:20',
            'customer_whatsapp'=> 'nullable|string|max:20',
            'price'            => 'nullable|numeric|min:0',
            'payment_method_id'=> 'nullable|integer|exists:payment_methods,id',
            'notes'            => 'nullable|string',
        ]);

        $slot = FieldSlot::findOrFail($validated['field_slot_id']);
        $validated['created_by'] = $request->user()->id;

        try {
            $reservation = $this->service->create($slot, $validated['reservation_date'], $validated);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($reservation->load('slot', 'member'), 201);
    }

    /** GET /api/field/reservations/{reservation} */
    public function show(FieldReservation $reservation): JsonResponse
    {
        return response()->json($reservation->load(['slot', 'member', 'paymentMethod', 'createdBy:id,name']));
    }

    /** PUT /api/field/reservations/{reservation} */
    public function update(Request $request, FieldReservation $reservation): JsonResponse
    {
        $validated = $request->validate([
            'customer_name'    => 'nullable|string|max:255',
            'customer_phone'   => 'nullable|string|max:20',
            'customer_whatsapp'=> 'nullable|string|max:20',
            'status'           => 'nullable|in:confirmed,cancelled,no_show,completed',
            'payment_status'   => 'nullable|in:pending,partial,paid,refunded',
            'payment_method_id'=> 'nullable|integer|exists:payment_methods,id',
            'price'            => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string',
        ]);

        $reservation->update($validated);

        return response()->json($reservation->fresh()->load('slot', 'member'));
    }

    /** DELETE /api/field/reservations/{reservation} */
    public function destroy(FieldReservation $reservation): JsonResponse
    {
        $reservation->update(['status' => 'cancelled']);
        $reservation->delete();

        return response()->json(null, 204);
    }

    /** POST /api/field/reservations/series */
    public function storeSeries(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'field_slot_id'      => 'required|integer|exists:field_slots,id',
            'start_date'         => 'required|date',
            'end_date'           => 'nullable|date|after:start_date',
            'days_of_week'       => 'required|array|min:1',
            'days_of_week.*'     => 'integer|between:1,7',
            'member_id'          => 'nullable|integer|exists:members,id',
            'customer_name'      => 'nullable|string|max:255',
            'customer_phone'     => 'nullable|string|max:20',
            'price_per_session'  => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string',
        ]);

        $slot = FieldSlot::findOrFail($validated['field_slot_id']);
        $validated['created_by'] = $request->user()->id;

        $series = $this->service->createSeries($slot, $validated);

        return response()->json($series->load('reservations'), 201);
    }
}
