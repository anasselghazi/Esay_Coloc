@extends('layouts.app')

@section('title', "Invitations - {$colocation->nom}")

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('colocations.show', $colocation) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">← Retour</a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">Invitations envoyées</h1>
            <p class="text-gray-600">Gérez les invitations par email pour votre colocation.</p>
        </div>
        <a href="{{ route('invitations.create', $colocation) }}" class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition shadow-lg">+ Inviter quelqu'un</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <ul class="divide-y divide-gray-200">
            @forelse($invitations as $inv)
            <li class="flex justify-between items-center px-6 py-4">
                <div class="space-y-1">
                    <p class="font-semibold text-gray-900">{{ $inv->email }}</p>
                    <p class="text-xs text-gray-500">Statut: {{ ucfirst($inv->status) }} - expire le {{ $inv->expires_at->format('d/m/Y') }}</p>
                </div>
                <div class="space-x-3 text-sm">
                    <form method="POST" action="{{ route('invitations.destroy', [$colocation, $inv]) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Supprimer cette invitation?')">Supprimer</button>
                    </form>
                </div>
            </li>
            @empty
            <li class="px-6 py-4 text-center text-gray-500">Aucune invitation envoyée.</li>
            @endforelse
        </ul>
    </div>

    {{ $invitations->links() }}
</div>
@endsection