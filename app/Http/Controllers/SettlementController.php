<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettlementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    
     // Display settlement view for a colocation
     
    public function index(Colocation $colocation)
    {
        // Check authorization
        if (!$colocation->isMemberActive(Auth::id())) {
            abort(403);
        }

        $settlements = $this->calculateSettlements($colocation);
        $payments = $colocation->payments()->where('status', 'pending')->get();

        return view('settlements.index', compact('colocation', 'settlements', 'payments'));
    }

    
     // Calculate who owes whom
     
    private function calculateSettlements(Colocation $colocation)
    {
        $activeMembers = $colocation->getActiveMembers();
        $expenses = $colocation->expenses;

        // Calculate total paid by each member
        $totalPaid = [];
        foreach ($activeMembers as $member) {
            $totalPaid[$member->id] = $expenses
                ->where('payer_id', $member->id)
                ->sum('amount');
        }

        // Calculate total expense amount and per-member share
        $totalAmount = $expenses->sum('amount');
        $memberCount = $activeMembers->count();
        $perPersonShare = $memberCount > 0 ? $totalAmount / $memberCount : 0;

        // Calculate balances
        $balances = [];
        foreach ($activeMembers as $member) {
            $paid = $totalPaid[$member->id] ?? 0;
            $balances[$member->id] = $paid - $perPersonShare;
        }

        // Calculate settlements (who owes whom)
        $settlements = [];
        $debtors = [];
        $creditors = [];

        foreach ($balances as $userId => $balance) {
            if ($balance < -0.01) {
                $debtors[$userId] = abs($balance);
            } elseif ($balance > 0.01) {
                $creditors[$userId] = $balance;
            }
        }

        foreach ($debtors as $debtorId => $debt) {
            foreach ($creditors as $creditorId => $credit) {
                if ($credit > 0.01) {
                    $amount = min($debt, $credit);
                    $settlements[] = [
                        'from' => $debtorId,
                        'from_user' => $activeMembers->find($debtorId),
                        'to' => $creditorId,
                        'to_user' => $activeMembers->find($creditorId),
                        'amount' => round($amount, 2),
                    ];
                    $debtors[$debtorId] -= $amount;
                    $creditors[$creditorId] -= $amount;
                }
            }
        }

        return [
            'settlements' => $settlements,
            'balances' => $balances,
            'activeMembers' => $activeMembers,
        ];
    }

    
     // Mark a payment as paid
    
    public function markPaid(Request $request, Colocation $colocation)
    {
        
        if (!$colocation->isMemberActive(Auth::id())) {
            abort(403);
        }

        $validated = $request->validate([
            'from_user_id' => 'required|exists:users,id',
            'to_user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        // Create or update payment
        $payment = Payment::firstOrCreate(
            [
                'colocation_id' => $colocation->id,
                'from_user_id' => $validated['from_user_id'],
                'to_user_id' => $validated['to_user_id'],
            ],
            [
                'amount' => $validated['amount'],
                'status' => 'paid',
                'paid_at' => now(),
            ]
        );

        if ($payment->status === 'pending') {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'amount' => $validated['amount'],
            ]);
        }

        return redirect()
            ->route('settlements.index', $colocation)
            ->with('status', 'Paiement enregistré.');
    }
}
