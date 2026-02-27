@extends('layouts.app')

@section('title', 'Mes Colocations')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mes Colocations</h1>
            <p class="text-gray-600 mt-2">Gérez vos colocations et vos dépenses partagées.</p>
        </div>
        <a href="{{ route('colocations.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-lg">
            + Créer une colocation
        </a>
    </div>

    <!-- Colocations Grid -->
    @if ($colocations->count() > 0)
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($colocations as $colocation)
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition border border-gray-100">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $colocation->nom }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            @if ($colocation->owner_id === Auth::id())
                            <span class="inline-block bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Propriétaire</span>
                            @else
                            <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Membre</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Members -->
                <div class="mb-4">
                    <p class="text-xs text-gray-500 font-semibold uppercase">Membres</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach ($colocation->getActiveMembers() as $member)
                        <div class="flex items-center gap-2 bg-gray-50 px-3 py-1 rounded-full">
                            <div class="w-6 h-6 bg-gradient-to-br from-indigo-400 to-purple-400 rounded-full flex items-center justify-center">
                                <span class="text-xs text-white font-bold">{{ substr($member->name, 0, 1) }}</span>
                            </div>
                            <span class="text-xs text-gray-700">{{ $member->name }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 mb-4 py-3 border-y border-gray-100">
                    <div>
                        <p class="text-xs text-gray-500">Dépenses</p>
                        <p class="text-lg font-bold text-gray-900">{{ $colocation->expenses->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="text-lg font-bold text-indigo-600">{{ number_format($colocation->expenses->sum('amount'), 2) }}€</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <a href="{{ route('colocations.show', $colocation) }}" class="flex-1 px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition text-center text-sm font-medium">
                        Voir
                    </a>
                    @if ($colocation->owner_id === Auth::id())
                    <a href="{{ route('colocations.edit', $colocation) }}" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-center text-sm font-medium">
                        Éditer
                    </a>
                    @else
                    <form method="POST" action="{{ route('colocations.leave', $colocation) }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-medium">
                            Quitter
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12 bg-white rounded-xl border border-gray-100">
        <div class="text-5xl mb-4">🏠</div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Pas encore de colocation</h3>
        <p class="text-gray-600 mb-6">Créez une nouvelle colocation ou rejoignez-en une via invitation.</p>
        <a href="{{ route('colocations.create') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            Créer ma première colocation
        </a>
    </div>
    @endif
</div>
@endsection
