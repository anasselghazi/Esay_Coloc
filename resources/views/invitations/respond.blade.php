@extends('layouts.app')

@section('title', 'Invitation')

@section('content')
<div class="max-w-md mx-auto space-y-6 mt-12">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Invitation à rejoindre {{ $colocation->nom }}</h1>
        <p class="text-gray-600 mb-6">Vous avez été invité par <strong>{{ $invitation->invitedBy->name }}</strong>.</p>
        <form method="POST" action="{{ route('invitations.accept') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $invitation->token }}">
            <input type="hidden" name="email" value="{{ $invitation->email }}">
            <button type="submit" class="w-full px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">Accepter</button>
        </form>
        <form method="POST" action="{{ route('invitations.decline') }}" class="mt-2">
            @csrf
            <input type="hidden" name="token" value="{{ $invitation->token }}">
            <input type="hidden" name="email" value="{{ $invitation->email }}">
            <button type="submit" class="w-full px-6 py-3 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">Refuser</button>
        </form>
    </div>
</div>
@endsection