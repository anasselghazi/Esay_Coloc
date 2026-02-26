<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Mes colocations actives
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <a href="{{ route('colocations.create') }}" class="underline">Créer une colocation</a>

                    @if($colocations->isEmpty())
                        <p class="mt-4">Vous n'avez aucune colocation active.</p>
                    @else
                        <ul class="mt-4 space-y-2">
                            @foreach($colocations as $coloc)
                                <li>
                                    <a href="{{ route('colocations.show', $coloc) }}" class="text-blue-500 underline">
                                        {{ $coloc->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
