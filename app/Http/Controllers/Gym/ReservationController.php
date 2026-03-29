<?php

namespace App\Http\Controllers\Gym;

use App\Http\Controllers\Controller;
use App\Models\FieldTimeSlot;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use App\Models\ReservationPayment;
use App\Services\Gym\ReservationService;
use App\Services\Gym\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService,
        protected WhatsAppService $whatsApp
    ) {}

    public function index(Request $request)
    {
        $view = $request->input('view', 'weekly');
        $dateParam = $request->input('date', Carbon::today()->toDateString());
        $date = Carbon::parse($dateParam);

        if ($view === 'weekly') {
            $weekStart    = $date->copy()->startOfWeek(Carbon::MONDAY);
            $calendar     = $this->reservationService->getWeeklyCalendar($weekStart);
            $timeSlots    = FieldTimeSlot::where('active', true)->orderBy('starts_at')->get();
            return view('gym.reservations.calendar', compact('calendar', 'weekStart', 'timeSlots', 'view', 'date'));
        }

        // Daily view
        $reservations = Reservation::with('timeSlot')
            ->forDate($date)
            ->where('status', '!=', 'cancelled')
            ->orderBy('field_time_slot_id')
            ->get();
        $timeSlots = FieldTimeSlot::where('active', true)->orderBy('starts_at')->get();

        return view('gym.reservations.calendar', compact('reservations', 'timeSlots', 'view', 'date'));
    }

    public function create(Request $request)
    {
        $timeSlots      = FieldTimeSlot::where('active', true)->orderBy('starts_at')->get();
        $paymentMethods = PaymentMethod::where('active', true)->get();
        $selectedDate   = $request->input('date', Carbon::today()->toDateString());
        $selectedSlot   = $request->integer('slot_id');
        return view('gym.reservations.create', compact('timeSlots', 'paymentMethods', 'selectedDate', 'selectedSlot'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'field_time_slot_id'  => 'required|exists:field_time_slots,id',
            'customer_name'       => 'required|string|max:255',
            'customer_phone'      => 'nullable|string|max:50',
            'customer_whatsapp'   => 'nullable|string|max:50',
            'reservation_date'    => 'required|date',
            'type'                => 'required|in:occasional,recurring',
            'recurrence_day'      => 'required_if:type,recurring|nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'recurring_until'     => 'nullable|date|after:reservation_date',
            'amount'              => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string',
        ]);

        try {
            if ($data['type'] === 'recurring') {
                $reservations = $this->reservationService->createRecurring($data);
                $reservation  = $reservations[0] ?? null;
            } else {
                $reservation = $this->reservationService->create($data);
            }
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        // Send WhatsApp confirmation
        if ($reservation && $reservation->customer_whatsapp) {
            $this->whatsApp->sendFromTemplate('reservation_confirmed', $reservation->customer_whatsapp, [
                'nombre' => $reservation->customer_name,
                'fecha'  => $reservation->reservation_date->format('d/m/Y'),
                'turno'  => $reservation->timeSlot->label ?? '',
            ]);
        }

        return redirect()->route('gym.reservations.index', ['date' => $data['reservation_date']])
            ->with('success', 'Reserva creada.');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['timeSlot', 'payments.paymentMethod']);
        $paymentMethods = PaymentMethod::where('active', true)->get();
        return view('gym.reservations.show', compact('reservation', 'paymentMethods'));
    }

    public function edit(Reservation $reservation)
    {
        $timeSlots = FieldTimeSlot::where('active', true)->orderBy('starts_at')->get();
        return view('gym.reservations.edit', compact('reservation', 'timeSlots'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'customer_name'     => 'required|string|max:255',
            'customer_phone'    => 'nullable|string|max:50',
            'customer_whatsapp' => 'nullable|string|max:50',
            'reservation_date'  => 'required|date',
            'status'            => 'required|in:confirmed,cancelled,pending',
            'amount'            => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string',
        ]);

        $reservation->update($data);

        return redirect()->route('gym.reservations.show', $reservation)
            ->with('success', 'Reserva actualizada.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->update(['status' => 'cancelled']);
        return redirect()->route('gym.reservations.index')
            ->with('success', 'Reserva cancelada.');
    }

    /**
     * Register a payment for a reservation.
     */
    public function registerPayment(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'amount'            => 'required|numeric|min:0.01',
            'paid_at'           => 'required|date',
            'reference'         => 'nullable|string|max:255',
            'notes'             => 'nullable|string',
        ]);

        ReservationPayment::create(array_merge($data, ['reservation_id' => $reservation->id]));
        $reservation->update(['payment_status' => 'paid']);

        return back()->with('success', 'Pago de reserva registrado.');
    }
}
