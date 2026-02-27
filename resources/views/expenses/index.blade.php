@extends('layouts.app')

@section('title', "Dépenses - {$colocation->nom}")

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('colocations.show', $colocation) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">← Retour</a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">Dépenses</h1>
            <p class="text-gray-600">Toutes les dépenses partagées dans la colocation.</p>
        </div>
        <a href="{{ route('expenses.create', $colocation) }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-lg">+ Nouvelle dépense</a>
    </div>

    <!-- month filter -->
    <form method="GET" class="flex items-center gap-2">
        <label for="month" class="text-sm text-gray-700">Mois :</label>
        <input type="month" name="month" id="month" value="{{ $month }}" class="border-gray-300 rounded-lg">
        <button type="submit" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition text-sm">Filtrer</button>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-xl shadow-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payeur</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($expenses as $expense)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $expense->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $expense->category?->name ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $expense->payer->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($expense->amount, 2) }}€</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                        @if($expense->payer_id === Auth::id() || $colocation->owner_id === Auth::id())
                        <a href="{{ route('expenses.edit', [$colocation, $expense]) }}" class="text-indigo-600 hover:text-indigo-800">Éditer</a>
                        <form method="POST" action="{{ route('expenses.destroy', [$colocation, $expense]) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Confirmer la suppression?')">Supprimer</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucune dépense enregistrée.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $expenses->withQueryString()->links() }}
</div>
@endsection