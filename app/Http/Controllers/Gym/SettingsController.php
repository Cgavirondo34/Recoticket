<?php

namespace App\Http\Controllers\Gym;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use App\Models\FieldTimeSlot;
use App\Models\MembershipPlan;
use App\Models\Partner;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Trainer;
use App\Models\WhatsappTemplate;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $plans      = MembershipPlan::orderBy('name')->get();
        $timeSlots  = FieldTimeSlot::orderBy('starts_at')->get();
        $partners   = Partner::orderBy('name')->get();
        $categories = ExpenseCategory::orderBy('name')->get();
        $methods    = PaymentMethod::orderBy('name')->get();
        $templates  = WhatsappTemplate::orderBy('event')->get();
        $trainers   = Trainer::where('active', true)->orderBy('full_name')->get();
        $events     = WhatsappTemplate::events();

        return view('gym.settings.index', compact(
            'plans', 'timeSlots', 'partners', 'categories',
            'methods', 'templates', 'trainers', 'events'
        ));
    }

    // ── Membership Plans ────────────────────────────────────────────────────

    public function storePlan(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
        ]);
        MembershipPlan::create($data + ['active' => true]);
        return back()->with('success', 'Plan creado.');
    }

    // ── Field Time Slots ────────────────────────────────────────────────────

    public function storeSlot(Request $request)
    {
        $data = $request->validate([
            'label'     => 'required|string|max:100',
            'starts_at' => 'required|date_format:H:i',
            'ends_at'   => 'required|date_format:H:i|after:starts_at',
            'price'     => 'required|numeric|min:0',
        ]);
        FieldTimeSlot::create($data + ['active' => true]);
        return back()->with('success', 'Turno creado.');
    }

    public function updateSlot(Request $request, FieldTimeSlot $slot)
    {
        $data = $request->validate([
            'label'     => 'required|string|max:100',
            'starts_at' => 'required|date_format:H:i',
            'ends_at'   => 'required|date_format:H:i',
            'price'     => 'required|numeric|min:0',
            'active'    => 'boolean',
        ]);
        $slot->update($data);
        return back()->with('success', 'Turno actualizado.');
    }

    // ── Partners ────────────────────────────────────────────────────────────

    public function storePartner(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email',
            'gym_percentage'   => 'required|numeric|min:0|max:100',
            'field_percentage' => 'required|numeric|min:0|max:100',
        ]);
        Partner::create($data + ['active' => true]);
        return back()->with('success', 'Socio creado.');
    }

    public function updatePartner(Request $request, Partner $partner)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'gym_percentage'   => 'required|numeric|min:0|max:100',
            'field_percentage' => 'required|numeric|min:0|max:100',
        ]);
        $partner->update($data);
        return back()->with('success', 'Socio actualizado.');
    }

    // ── WhatsApp Templates ──────────────────────────────────────────────────

    public function storeTemplate(Request $request)
    {
        $data = $request->validate([
            'event' => 'required|string|max:100',
            'name'  => 'required|string|max:255',
            'body'  => 'required|string',
        ]);
        WhatsappTemplate::create($data + ['active' => true]);
        return back()->with('success', 'Plantilla creada.');
    }

    public function updateTemplate(Request $request, WhatsappTemplate $template)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'body'   => 'required|string',
            'active' => 'boolean',
        ]);
        $template->update($data);
        return back()->with('success', 'Plantilla actualizada.');
    }

    // ── Expense Categories ──────────────────────────────────────────────────

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'color' => 'required|string|max:20',
        ]);
        ExpenseCategory::create($data);
        return back()->with('success', 'Categoría creada.');
    }

    // ── Trainers ────────────────────────────────────────────────────────────

    public function storeTrainer(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'nullable|email',
            'phone'     => 'nullable|string|max:50',
            'specialty' => 'nullable|string|max:255',
        ]);
        Trainer::create($data + ['active' => true]);
        return back()->with('success', 'Entrenador creado.');
    }
}
