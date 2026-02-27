@extends('layouts.app')

@section('title', $colocation->nom)

@section('content')
<div class="space-y-8">
    <!-- Header with breadcrumb -->
    <div>
        <a href="{{ route('colocations.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            ← Retour
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-4">{{ $colocation->nom }}</h1>
        <div class="flex items-center gap-4 mt-2">
            <span class="inline-block {{ $colocation->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-3 py-1 rounded-full text-sm font-medium">
                {{ $colocation->status === 'active' ? 'Active' : 'Annulée' }}
            </span>
            <span class="text-gray-600">Propriétaire: <strong>{{ $colocation->owner->name }}</strong></span>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Members Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Membres</h2>
                    @if ($colocation->owner_id === Auth::id())
                    <a href="{{ route('invitations.create', $colocation) }}" class="text-sm px-3 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition">
                        + Inviter
                    </a>
                    @endif
                </div>
                <div class="space-y-3">
                    @forelse ($colocation->getActiveMembers() as $member)
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-400 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold">{{ substr($member->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">
                                {{ $member->name }}
                                <span class="text-xs text-gray-500">({{ $member->reputation }} pts)</span>
                            </p>
                            <p class="text-xs text-gray-500">
                                @if ($member->id === $colocation->owner_id)
                                <span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded text-xs font-medium">Propriétaire</span>
                                @else
                                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs font-medium">Membre</span>
                                @endif
                            </p>
                            </div>
                        </div>
                        @if ($colocation->owner_id === Auth::id() && $member->id !== $colocation->owner_id)
                        <form method="POST" action="{{ route('colocations.remove-member', $colocation) }}" class="inline">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $member->id }}">
                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium" onclick="return confirm('Êtes-vous sûr?')">
                                Retirer
                            </button>
                        </form>
                        @endif
                    </div>
                    @empty
                    <p class="text-gray-500 py-4">Pas encore de membres actifs.</p>
                    @endforelse
                </div>
            </div>

            <!-- Expenses Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Dépenses Récentes</h2>
                    <a href="{{ route('expenses.index', $colocation) }}" class="text-sm px-3 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition">
                        Voir tout
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse ($colocation->expenses()->latest()->limit(5)->get() as $expense)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg text-sm">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $expense->title }}</p>
                            <p class="text-xs text-gray-500">par {{ $expense->payer->name }} • {{ $expense->expense_date->format('d/m/Y') }}</p>
                        </div>
                        <p class="font-bold text-indigo-600">{{ number_format($expense->amount, 2) }}€</p>
                    </div>
                    @empty
                    <p class="text-gray-500 py-4">Aucune dépense pour le moment.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('expenses.create', $colocation) }}" class="block w-full px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition text-center font-medium text-sm">
                        + Ajouter une dépense
                    </a>
                    <a href="{{ route('settlements.index', $colocation) }}" class="block w-full px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-center font-medium text-sm">
                        Voir les comptes
                    </a>
                    @if ($colocation->owner_id === Auth::id())
                    <a href="{{ route('categories.index', $colocation) }}" class="block w-full px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition text-center font-medium text-sm">
                        Gérer catégories
                    </a>
                    <a href="{{ route('invitations.index', $colocation) }}" class="block w-full px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition text-center font-medium text-sm">
                        Invitations
                    </a>
                    @endif
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4">Statistiques</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Total dépensé</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ number_format($colocation->expenses->sum('amount'), 2) }}€</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Nom. de dépenses</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $colocation->expenses->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Membres actifs</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $colocation->getActiveMembers()->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            @if ($colocation->owner_id === Auth::id())
            <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
                <h3 class="font-bold text-red-900 mb-4">Zone dangereuse</h3>
                <form method="POST" action="{{ route('colocations.cancel', $colocation) }}" onsubmit="return confirm('Êtes-vous sûr? Cette action ne peut pas être annulée.')">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition font-medium text-sm">
                        Annuler la colocation
                    </button>
                </form>
            </div>
            @elseif ($colocation->status === 'active')
            <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
                <form method="POST" action="{{ route('colocations.leave', $colocation) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir quitter?')">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition font-medium text-sm">
                        Quitter la colocation
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
