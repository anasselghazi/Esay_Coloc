@extends('layouts.app')

@section('title', 'Inviter un membre')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div>
        <a href="{{ route('invitations.index', $colocation) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">← Retour</a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Inviter un membre</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('invitations.store', $colocation) }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">Email du destinataire</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-yellow-500">
                @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-4 pt-4">
                <a href="{{ route('invitations.index', $colocation) }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Annuler</a>
                <button type="submit" class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">Envoyer l'invitation</button>
            </div>
        </form>
    </div>
</div>
@endsection