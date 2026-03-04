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
    

    
     //Afficher la page des règlements (Qui doit payer qui).
     
    public function index(Colocation $colocation)
    {
        // Vérifier si l'utilisateur est un membre actif de la colocation
        if (!$colocation->isMemberActive(Auth::id())) {
            abort(403, "Désolé, vous n'êtes pas un membre actif de cette colocation.");
        }

        // Appeler la fonction de calcul pour déterminer les dettes et les balances
        $donnéesCalcul = $this->calculateSettlements($colocation);
        
        // Récupérer l'historique des paiements 
        $paiementsEffectués = $colocation->payments()
            ->where('status', 'paid')
            ->latest('paid_at')
            ->get();

        return view('settlements.index', [
            'colocation'     => $colocation,
            'settlements'    => $donnéesCalcul['settlements'], 
            'balances'       => $donnéesCalcul['balances'],   
            'activeMembers'  => $donnéesCalcul['activeMembers'],
            'payments'       => $paiementsEffectués,  
        ]  );       
    }

    
      //Logique de calcul des dettes 
     
    private function calculateSettlements(Colocation $colocation)
    {
        $membresActifs = $colocation->getActiveMembers();
        $dépenses = $colocation->expenses;

        // Calculer le total payé par chaque membre
        $totalPayéParMembre = [];
        foreach ($membresActifs as $membre) {
            $totalPayéParMembre[$membre->id] = $dépenses
                ->where('payer_id', $membre->id)
                ->sum('amount');
        }

        // 2. Calculer la part théorique par personne (Moyenne)
        $montantTotal = $dépenses->sum('amount');
        $nombreMembres = $membresActifs->count();
        $partParPersonne = $nombreMembres > 0 ? $montantTotal / $nombreMembres : 0;

        // 3. Calculer les balances 
        $balances = [];
        $débiteurs = [];  
        $créanciers = []; 

        foreach ($membresActifs as $membre) {
            $déjàPayé = $totalPayéParMembre[$membre->id] ?? 0;
            $solde = $déjàPayé - $partParPersonne;
            $balances[$membre->id] = $solde;

            if ($solde < -0.01) {
                $débiteurs[$membre->id] = abs($solde);
            } elseif ($solde > 0.01) {
                $créanciers[$membre->id] = $solde;
            }
        }

        // 4. Algorithme de compensation pour générer les suggestions de transfert
        $suggestionsRèglements = [];
        foreach ($débiteurs as $idDébiteur => $montantDû) {
            foreach ($créanciers as $idCréancier => $montantÀRecevoir) {
                if ($montantÀRecevoir > 0.01 && $montantDû > 0.01) {
                    $montantTransfert = min($montantDû, $montantÀRecevoir);

                    $suggestionsRèglements[] = [
                        'from_id'   => $idDébiteur,
                        'from_user' => $membresActifs->find($idDébiteur),
                        'to_id'     => $idCréancier,
                        'to_user'   => $membresActifs->find($idCréancier),
                        'amount'    => round($montantTransfert, 2),
                    ];

                    $montantDû -= $montantTransfert;
                    $créanciers[$idCréancier] -= $montantTransfert;
                }
            }
        }

        return [
            'settlements'   => $suggestionsRèglements,
            'balances'      => $balances,
            'activeMembers' => $membresActifs,
        ];
    }

    
     // Enregistrer une nouvelle opération de paiement.
    
    public function markPaid(Request $request, Colocation $colocation)
    {
        if (!$colocation->isMemberActive(Auth::id())) {
            abort(403);
        }

        // Validation des données entrantes
        $donnéesValidées = $request->validate([
            'from_user_id' => 'required|exists:users,id',
            'to_user_id'   => 'required|exists:users,id',
            'amount'       => 'required|numeric|min:0.01',
        ]);

        try {
            // Utilisation d'une transaction pour garantir l'intégrité des données
            DB::transaction(function () use ($colocation, $donnéesValidées) {
                Payment::create([
                    'colocation_id' => $colocation->id,
                    'from_user_id'  => $donnéesValidées['from_user_id'],
                    'to_user_id'    => $donnéesValidées['to_user_id'],
                    'amount'        => $donnéesValidées['amount'],
                    'status'        => 'paid',
                    'paid_at'       => now(),
                ]);
            });

            return redirect()
                ->route('settlements.index', $colocation)
                ->with('status', 'Le paiement a été enregistré avec succès.');

        } catch (\Exception $e) {
            return back()->withErrors("Une erreur est survenue lors de l'enregistrement du paiement.");
        }
    }
}