@extends('layouts.guest')

@section('title', 'Registrarse')

@section('content')
<div class="bg-white rounded-2xl shadow-xl p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Crear cuenta</h2>

    <form method="POST" action="/register" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                class="w-full border @error('name') border-red-400 @else border-gray-300 @enderror rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full border @error('email') border-red-400 @else border-gray-300 @enderror rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <input type="password" name="password" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de cuenta</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center gap-2 border border-gray-200 rounded-lg p-3 cursor-pointer hover:border-indigo-400 transition">
                    <input type="radio" name="role" value="buyer" {{ old('role', 'buyer') === 'buyer' ? 'checked' : '' }} class="text-indigo-600">
                    <div>
                        <div class="text-sm font-semibold text-gray-700">🛍 Comprador</div>
                        <div class="text-xs text-gray-400">Comprá entradas</div>
                    </div>
                </label>
                <label class="flex items-center gap-2 border border-gray-200 rounded-lg p-3 cursor-pointer hover:border-indigo-400 transition">
                    <input type="radio" name="role" value="organizer" {{ old('role') === 'organizer' ? 'checked' : '' }} class="text-indigo-600">
                    <div>
                        <div class="text-sm font-semibold text-gray-700">🎪 Organizador</div>
                        <div class="text-xs text-gray-400">Creá eventos</div>
                    </div>
                </label>
            </div>
        </div>
        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg font-semibold transition">
            Crear cuenta
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        ¿Ya tenés cuenta?
        <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:underline">Iniciá sesión</a>
    </p>
</div>
@endsection
