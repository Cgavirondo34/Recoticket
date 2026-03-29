@extends('layouts.gym')

@section('title', 'Configuración')

@section('content')
<h1 class="text-xl font-bold text-slate-800 mb-6">Configuración</h1>

{{-- Tab navigation --}}
<div class="flex gap-1 border-b mb-6 overflow-x-auto">
    @foreach([
        'plans'     => '📋 Planes',
        'slots'     => '⏰ Turnos',
        'partners'  => '🤝 Socios empresa',
        'trainers'  => '💪 Entrenadores',
        'categories'=> '🏷️ Categorías',
        'templates' => '💬 WhatsApp',
    ] as $tab => $label)
        <button onclick="showTab('{{ $tab }}')" id="tab-{{ $tab }}"
            class="tab-btn px-4 py-2 text-sm font-medium border-b-2 transition whitespace-nowrap
                   {{ $loop->first ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            {{ $label }}
        </button>
    @endforeach
</div>

{{-- Plans tab --}}
<div id="pane-plans" class="tab-pane">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-5">
            <h3 class="font-semibold text-slate-700 mb-4">Planes actuales</h3>
            @foreach($plans as $plan)
                <div class="flex items-center justify-between py-2 border-b last:border-0 text-sm">
                    <div>
                        <span class="font-medium text-slate-700">{{ $plan->name }}</span>
                        <span class="text-slate-400 ml-2">${{ number_format($plan->price, 0, ',', '.') }} / {{ $plan->duration_days }}d</span>
                    </div>
                    <span class="{{ $plan->active ? 'text-green-500' : 'text-slate-300' }} text-xs">{{ $plan->active ? 'Activo' : 'Inactivo' }}</span>
                </div>
            @endforeach
        </div>
        <div class="card p-5">
            <h3 class="font-semibold text-slate-700 mb-4">Nuevo plan</h3>
            <form method="POST" action="{{ route('gym.settings.plans.store') }}" class="space-y-3">
                @csrf
                <div><label class="form-label text-xs">Nombre *</label><input type="text" name="name" required class="form-input" placeholder="Plan mensual"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="form-label text-xs">Precio ($) *</label><input type="number" name="price" min="0" step="0.01" required class="form-input"></div>
                    <div><label class="form-label text-xs">Duración (días) *</label><input type="number" name="duration_days" value="30" min="1" required class="form-input"></div>
                </div>
                <button type="submit" class="btn-primary">Crear plan</button>
            </form>
        </div>
    </div>
</div>

{{-- Time slots tab --}}
<div id="pane-slots" class="tab-pane hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-5">
            <h3 class="font-semibold text-slate-700 mb-4">Turnos configurados</h3>
            @foreach($timeSlots as $slot)
                <div class="flex items-center justify-between py-2 border-b last:border-0 text-sm">
                    <div>
                        <span class="font-medium text-slate-700">{{ $slot->label }}</span>
                        <span class="text-slate-400 ml-2">{{ substr($slot->starts_at,0,5) }}–{{ substr($slot->ends_at,0,5) }}</span>
                        <span class="text-slate-400 ml-2">${{ number_format($slot->price, 0, ',', '.') }}</span>
                    </div>
                    <span class="{{ $slot->active ? 'text-green-500' : 'text-slate-300' }} text-xs">{{ $slot->active ? '●' : '○' }}</span>
                </div>
            @endforeach
        </div>
        <div class="card p-5">
            <h3 class="font-semibold text-slate-700 mb-4">Nuevo turno</h3>
            <form method="POST" action="{{ route('gym.settings.slots.store') }}" class="space-y-3">
                @csrf
                <div><label class="form-label text-xs">Etiqueta *</label><input type="text" name="label" required class="form-input" placeholder="Turno mañana"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="form-label text-xs">Inicio *</label><input type="time" name="starts_at" required class="form-input"></div>
                    <div><label class="form-label text-xs">Fin *</label><input type="time" name="ends_at" required class="form-input"></div>
                </div>
                <div><label class="form-label text-xs">Precio ($) *</label><input type="number" name="price" min="0" step="0.01" required class="form-input"></div>
                <button type="submit" class="btn-primary">Crear turno</button>
            </form>
        </div>
    </div>
</div>

{{-- Partners tab --}}
<div id="pane-partners" class="tab-pane hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-5">
            <h3 class="font-semibold text-slate-700 mb-4">Socios de la empresa</h3>
            @foreach($partners as $partner)
                <div class="py-3 border-b last:border-0 text-sm">
                    <div class="font-medium text-slate-700 mb-1">{{ $partner->name }}</div>
                    <div class="text-slate-400 text-xs">
                        Gym: {{ $partner->gym_percentage }}% · Cancha: {{ $partner->field_percentage }}%
                    </div>
                </div>
            @endforeach
        </div>
        <div class="card p-5">
            <h3 class="font-semibold text-slate-700 mb-4">Nuevo socio empresa</h3>
            <form method="POST" action="{{ route('gym.settings.partners.store') }}" class="space-y-3">
                @csrf
                <div><label class="form-label text-xs">Nombre *</label><input type="text" name="name" required class="form-input"></div>
                <div><label class="form-label text-xs">Email</label><input type="email" name="email" class="form-input"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="form-label text-xs">% Gym *</label><input type="number" name="gym_percentage" value="50" min="0" max="100" required class="form-input"></div>
                    <div><label class="form-label text-xs">% Cancha *</label><input type="number" name="field_percentage" value="50" min="0" max="100" required class="form-input"></div>
                </div>
                <button type="submit" class="btn-primary">Crear socio</button>
            </form>
        </div>
    </div>
</div>

{{-- Trainers tab --}}
<div id="pane-trainers" class="tab-pane hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-5">
            <h3 class="font-semibold text-slate-700 mb-4">Entrenadores</h3>
            @foreach($trainers as $trainer)
                <div class="flex items-center justify-between py-2 border-b last:border-0 text-sm">
                    <div>
                        <span class="font-medium text-slate-700">{{ $trainer->full_name }}</span>
                        @if($trainer->specialty)
                            <span class="text-slate-400 ml-2">{{ $trainer->specialty }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="card p-5">
            <h3 class="font-semibold text-slate-700 mb-4">Nuevo entrenador</h3>
            <form method="POST" action="{{ route('gym.settings.trainers.store') }}" class="space-y-3">
                @csrf
                <div><label class="form-label text-xs">Nombre completo *</label><input type="text" name="full_name" required class="form-input"></div>
                <div><label class="form-label text-xs">Email</label><input type="email" name="email" class="form-input"></div>
                <div><label class="form-label text-xs">Teléfono</label><input type="text" name="phone" class="form-input"></div>
                <div><label class="form-label text-xs">Especialidad</label><input type="text" name="specialty" class="form-input" placeholder="Musculación, Funcional..."></div>
                <button type="submit" class="btn-primary">Crear entrenador</button>
            </form>
        </div>
    </div>
</div>

{{-- Expense categories tab --}}
<div id="pane-categories" class="tab-pane hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-5">
            <h3 class="font-semibold text-slate-700 mb-4">Categorías</h3>
            @foreach($categories as $cat)
                <div class="flex items-center gap-3 py-2 border-b last:border-0 text-sm">
                    <span class="w-3 h-3 rounded-full" style="background: {{ $cat->color }}"></span>
                    <span class="text-slate-700">{{ $cat->name }}</span>
                </div>
            @endforeach
        </div>
        <div class="card p-5">
            <h3 class="font-semibold text-slate-700 mb-4">Nueva categoría</h3>
            <form method="POST" action="{{ route('gym.settings.categories.store') }}" class="space-y-3">
                @csrf
                <div><label class="form-label text-xs">Nombre *</label><input type="text" name="name" required class="form-input"></div>
                <div><label class="form-label text-xs">Color</label><input type="color" name="color" value="#3b82f6" class="form-input w-16 h-10 p-1 cursor-pointer"></div>
                <button type="submit" class="btn-primary">Crear categoría</button>
            </form>
        </div>
    </div>
</div>

{{-- WhatsApp templates tab --}}
<div id="pane-templates" class="tab-pane hidden">
    <div class="space-y-5">
        @foreach($templates as $template)
            <div class="card p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <span class="font-semibold text-slate-700">{{ $template->name }}</span>
                        <span class="ml-2 text-xs text-slate-400">{{ $events[$template->event] ?? $template->event }}</span>
                    </div>
                    <span class="{{ $template->active ? 'badge-active' : 'badge-suspended' }}">{{ $template->active ? 'Activa' : 'Inactiva' }}</span>
                </div>
                <form method="POST" action="{{ route('gym.settings.templates.update', $template) }}" class="space-y-3">
                    @csrf @method('PUT')
                    <div><label class="form-label text-xs">Mensaje (usa {{"{{"}}nombre{{"}}"}}, {{"{{"}}fecha{{"}}"}}, etc.)</label>
                    <textarea name="body" rows="3" class="form-input font-mono text-xs">{{ $template->body }}</textarea></div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="active" value="1" {{ $template->active ? 'checked' : '' }} class="rounded">
                        <label class="text-xs text-slate-600">Activa</label>
                    </div>
                    <button type="submit" class="btn-secondary text-xs">Guardar plantilla</button>
                </form>
            </div>
        @endforeach

        <div class="card p-5">
            <h3 class="font-semibold text-slate-700 mb-4">Nueva plantilla</h3>
            <form method="POST" action="{{ route('gym.settings.templates.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="form-label text-xs">Evento *</label>
                    <select name="event" required class="form-input">
                        @foreach($events as $eventKey => $eventLabel)
                            <option value="{{ $eventKey }}">{{ $eventLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="form-label text-xs">Nombre *</label><input type="text" name="name" required class="form-input"></div>
                <div><label class="form-label text-xs">Mensaje *</label><textarea name="body" rows="3" required class="form-input font-mono text-xs" placeholder="Hola {{nombre}}, tu membresía vence el {{fecha}}."></textarea></div>
                <button type="submit" class="btn-primary">Crear plantilla</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTab(name) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('border-blue-600', 'text-blue-600');
        b.classList.add('border-transparent', 'text-slate-500');
    });
    document.getElementById('pane-' + name).classList.remove('hidden');
    const btn = document.getElementById('tab-' + name);
    btn.classList.add('border-blue-600', 'text-blue-600');
    btn.classList.remove('border-transparent', 'text-slate-500');
}
</script>
@endpush
@endsection
