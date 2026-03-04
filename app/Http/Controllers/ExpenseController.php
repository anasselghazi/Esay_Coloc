<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    
    
     // expenses for a colocation
     
    public function index(Colocation $colocation)
    {
        
        if (!$colocation->isMemberActive(Auth::id())) {
            abort(403);
        }

        $perPage = request('per_page', 10);
        $month = request('month', now()->format('Y-m'));

        $query = $colocation->expenses()
            ->with(['payer', 'category']);

        // Filter by month
        if ($month) {
            $query->whereYear('expense_date', substr($month, 0, 4))
                  ->whereMonth('expense_date', substr($month, 5, 2));
        }

        $expenses = $query->orderBy('expense_date', 'desc')
                         ->paginate($perPage);

        $categories = $colocation->categories;

        return view('expenses.index', compact('colocation', 'expenses', 'categories', 'month'));
    }

    /**
     * Show create expense form
     */
    public function create(Colocation $colocation)
    {
        
        if (!$colocation->isMemberActive(Auth::id())) {
            abort(403);
        }

        $categories = $colocation->categories;
        return view('expenses.create', compact('colocation', 'categories'));
    }

    
     // Store a new expense
     
    public function store(Request $request, Colocation $colocation)
    {
        
        if (!$colocation->isMemberActive(Auth::id())) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        $validated['colocation_id'] = $colocation->id;
        $validated['payer_id'] = Auth::id();

        Expense::create($validated);

        return redirect()
            ->route('expenses.index', $colocation)
            ->with('status', 'Dépense ajoutée avec succès.');
    }

    
     // Edit expense form
     
    public function edit(Colocation $colocation, Expense $expense)
    {
        
        if ($expense->colocation_id !== $colocation->id || 
            ($expense->payer_id !== Auth::id() && $colocation->owner_id !== Auth::id())) {
            abort(403);
        }

        $categories = $colocation->categories;
        return view('expenses.edit', compact('colocation', 'expense', 'categories'));
    }

    
     // Update expense
     
    public function update(Request $request, Colocation $colocation, Expense $expense)
    {
        
        if ($expense->colocation_id !== $colocation->id || 
            ($expense->payer_id !== Auth::id() && $colocation->owner_id !== Auth::id())) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()
            ->route('expenses.index', $colocation)
            ->with('status', 'Dépense mise à jour avec succès.');
    }

    
     // Delete expense
     
    public function destroy(Colocation $colocation, Expense $expense)
    {
        
        if ($expense->colocation_id !== $colocation->id || 
            ($expense->payer_id !== Auth::id() && $colocation->owner_id !== Auth::id())) {
            abort(403);
        }

        $expense->delete();

        return redirect()
            ->route('expenses.index', $colocation)
            ->with('status', 'Dépense supprimée.');
    }
}
