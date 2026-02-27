@extends('layouts.app')

@section('title', "Comptes - {$colocation->nom}")

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('colocations.show', $colocation) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">← Retour</a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">Comptes et remboursements</h1>
            <p class="text-gray-600">Qui doit à qui ?</p>
        </div>
    </div>

    <!-- Settlements table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">De</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">À</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($settlements['settlements'] as $s)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $s['from_user']->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $s['to_user']->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($s['amount'], 2) }}€</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                        <form method="POST" action="{{ route('settlements.mark-paid', $colocation) }}" class="inline">
                            @csrf
                            <input type="hidden" name="from_user_id" value="{{ $s['from'] }}">
                            <input type="hidden" name="to_user_id" value="{{ $s['to'] }}">
                            <input type="hidden" name="amount" value="{{ $s['amount'] }}">
                            <button type="submit" class="text-green-600 hover:text-green-800">Marquer payé</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if(empty($settlements['settlements']))
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucun règlement nécessaire. Tout est équilibré !</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection