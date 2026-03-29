<?php

namespace App\Http\Controllers\Gym;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class MembershipPlanController extends Controller
{
    public function index()
    {
        $plans = MembershipPlan::withCount('members')->orderBy('name')->get();
        return view('gym.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('gym.plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'description'        => 'nullable|string',
            'price'              => 'required|numeric|min:0',
            'duration_days'      => 'required|integer|min:1',
            'auto_renew_default' => 'boolean',
        ]);

        MembershipPlan::create($data + ['active' => true]);

        return redirect()->route('gym.plans.index')
            ->with('success', 'Plan creado.');
    }

    public function edit(MembershipPlan $plan)
    {
        return view('gym.plans.edit', compact('plan'));
    }

    public function update(Request $request, MembershipPlan $plan)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'description'        => 'nullable|string',
            'price'              => 'required|numeric|min:0',
            'duration_days'      => 'required|integer|min:1',
            'auto_renew_default' => 'boolean',
            'active'             => 'boolean',
        ]);

        $plan->update($data);

        return redirect()->route('gym.plans.index')
            ->with('success', 'Plan actualizado.');
    }

    public function destroy(MembershipPlan $plan)
    {
        $plan->delete();
        return redirect()->route('gym.plans.index')
            ->with('success', 'Plan eliminado.');
    }
}
