@extends('layouts.app')

@section('title', 'Usuarios — Admin')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Gestión de Usuarios</h1>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
            <tr>
                <th class="px-5 py-3 text-left">Nombre</th>
                <th class="px-5 py-3 text-left">Email</th>
                <th class="px-5 py-3 text-left">Rol actual</th>
                <th class="px-5 py-3 text-left">Registrado</th>
                <th class="px-5 py-3">Cambiar rol</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-700">{{ $user->name }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $user->email }}</td>
                <td class="px-5 py-3">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $user->role === 'organizer' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $user->role === 'buyer' ? 'bg-gray-100 text-gray-600' : '' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-400 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                <td class="px-5 py-3">
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex gap-2 items-center">
                        @csrf
                        <select name="role" class="border border-gray-200 rounded px-2 py-1 text-xs">
                            <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Comprador</option>
                            <option value="organizer" {{ $user->role === 'organizer' ? 'selected' : '' }}>Organizador</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        <button type="submit" class="bg-indigo-600 text-white text-xs px-3 py-1 rounded hover:bg-indigo-700 transition">Guardar</button>
                    </form>
                    @else
                        <span class="text-xs text-gray-400">(tú)</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
