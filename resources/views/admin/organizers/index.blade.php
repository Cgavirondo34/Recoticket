@extends('layouts.app')

@section('title', 'Organizadores — Admin')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Organizadores</h1>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
            <tr>
                <th class="px-5 py-3 text-left">Nombre</th>
                <th class="px-5 py-3 text-left">Usuario</th>
                <th class="px-5 py-3 text-left">Sitio web</th>
                <th class="px-5 py-3 text-center">Verificado</th>
                <th class="px-5 py-3 text-left">Registrado</th>
                <th class="px-5 py-3">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($organizers as $organizer)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-700">{{ $organizer->name }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $organizer->user?->email }}</td>
                <td class="px-5 py-3">
                    @if($organizer->website)
                        <a href="{{ $organizer->website }}" target="_blank" class="text-indigo-600 text-xs hover:underline">{{ $organizer->website }}</a>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-center">
                    @if($organizer->verified)
                        <span class="text-green-500 font-bold">✓ Sí</span>
                    @else
                        <span class="text-gray-400">No</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-gray-400 text-xs">{{ $organizer->created_at->format('d/m/Y') }}</td>
                <td class="px-5 py-3">
                    <form method="POST" action="{{ route('admin.organizers.verify', $organizer) }}">
                        @csrf
                        <button type="submit" class="text-xs px-3 py-1 rounded font-medium transition
                            {{ $organizer->verified ? 'bg-gray-100 hover:bg-gray-200 text-gray-600' : 'bg-blue-600 hover:bg-blue-700 text-white' }}">
                            {{ $organizer->verified ? 'Quitar verificación' : 'Verificar' }}
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $organizers->links() }}</div>
@endsection
