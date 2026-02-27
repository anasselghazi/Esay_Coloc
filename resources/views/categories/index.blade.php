@extends('layouts.app')

@section('title', "Catégories - {$colocation->nom}")

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('colocations.show', $colocation) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">← Retour</a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">Catégories</h1>
            <p class="text-gray-600">Gérez les catégories de dépenses pour cette colocation.</p>
        </div>
        <a href="{{ route('categories.create', $colocation) }}" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition shadow-lg">+ Nouvelle catégorie</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <ul class="divide-y divide-gray-200">
            @forelse($categories as $category)
            <li class="flex justify-between items-center px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full" style="background-color: {{ $category->color }}"></div>
                    <span class="font-semibold text-gray-900">{{ $category->name }}</span>
                </div>
                <div class="space-x-3 text-sm">
                    <a href="{{ route('categories.edit', [$colocation, $category]) }}" class="text-indigo-600 hover:text-indigo-800">Éditer</a>
                    <form method="POST" action="{{ route('categories.destroy', [$colocation, $category]) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Supprimer cette catégorie?')">Supprimer</button>
                    </form>
                </div>
            </li>
            @empty
            <li class="px-6 py-4 text-center text-gray-500">Aucune catégorie définie.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection