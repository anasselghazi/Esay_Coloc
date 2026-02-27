@extends('layouts.app')

@section('title', 'Modifier une catégorie')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div>
        <a href="{{ route('categories.index', $colocation) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">← Retour</a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Modifier la catégorie</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('categories.update', [$colocation, $category]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Nom</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-purple-500">
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="color" class="block text-sm font-semibold text-gray-900 mb-2">Couleur (hex)</label>
                <input type="text" name="color" id="color" value="{{ old('color', $category->color) }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-purple-500">
                @error('color')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-4 pt-4">
                <a href="{{ route('categories.index', $colocation) }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Annuler</a>
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection