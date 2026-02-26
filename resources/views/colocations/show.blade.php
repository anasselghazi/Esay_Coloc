<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $colocation->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>Status : {{ $colocation->status }}</p>
                    <p>Propriétaire : {{ $colocation->owner->name }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-semibold">Membres</h3>
                    <ul class="mt-2 space-y-1">
                        @foreach($colocation->member->where('pivot.left_at', null) as $member)
                            <li>{{ $member->name }} @if($member->pivot->role === 'owner')(propriétaire)@endif</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($colocation->status === 'active')
                        @if(auth()->user()->id === $colocation->owner_id)
                            <form method="POST" action="{{ route('colocations.cancel', $colocation) }}">
                                @csrf
                                <x-primary-button>{{ __('Annuler la colocation') }}</x-primary-button>
                            </form>
                        @else
                            @php
                                $joined = auth()->user()->colocations()
                                    ->where('colocations.id', $colocation->id)
                                    ->wherePivot('left_at', null)
                                    ->exists();
                            @endphp
                            @if($joined)
                                <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
                                    @csrf
                                    <x-primary-button>{{ __('Quitter la colocation') }}</x-primary-button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('colocations.join', $colocation) }}">
                                    @csrf
                                    <x-primary-button>{{ __('Rejoindre la colocation') }}</x-primary-button>
                                </form>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
