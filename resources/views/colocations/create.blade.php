@extends('layouts.app')

@section('title', 'Créer une colocation')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('colocations.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium flex items-center gap-2">
            ← Retour
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-4">Créer une nouvelle colocation</h1>
        <p class="text-gray-600 mt-2">Paramétrez votre nouvel espace de vie partagé.</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('colocations.store') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Nom de la colocation</label>
                <input type="text" name="name" id="name" placeholder="ex: Appartement Rue de la Paix" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       value="{{ old('name') }}" required>
                @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex gap-4 pt-4">
                <a href="{{ route('colocations.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Créer la colocation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
