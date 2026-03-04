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

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Règlements à effectuer</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">De</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">À</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($settlements as $s)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">
                            {{ $s['from_user']->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $s['to_user']->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-bold">
                            {{ number_format($s['amount'], 2) }}€
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            <form method="POST" action="{{ route('settlements.mark-paid', $colocation) }}" class="inline">
                                @csrf
                                <input type="hidden" name="from_user_id" value="{{ $s['from_id'] }}">
                                <input type="hidden" name="to_user_id" value="{{ $s['to_id'] }}">
                                <input type="hidden" name="amount" value="{{ $s['amount'] }}">
                                <button type="submit" class="bg-green-50 text-green-700 px-3 py-1 rounded-md border border-green-200 hover:bg-green-100 transition-colors text-xs font-bold uppercase">
                                    Marquer payé
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <span class="text-3xl mb-2">🎉</span>
                                <p>Tout est équilibré ! Aucun remboursement n'est nécessaire.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($payments) && $payments->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Historique des remboursements</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Détails</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($payments as $payment)
                    <tr>
                        <td class="px-6 py-3 whitespace-nowrap text-xs text-gray-500">
                            {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : $payment->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-700">
                            <span class="font-semibold">{{ $payment->fromUser->name }}</span> 
                            <span class="text-gray-400 mx-1">→</span> 
                            <span class="font-semibold">{{ $payment->toUser->name }}</span>
                        </td>
                        <td class="px-6 py-3 text-right text-sm font-medium text-green-600">
                            +{{ number_format($payment->amount, 2) }}€
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection