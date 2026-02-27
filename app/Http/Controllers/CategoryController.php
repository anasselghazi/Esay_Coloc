<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display categories for a colocation
     */
    public function index(Colocation $colocation)
    {
        // Check authorization - owner only
        if ($colocation->owner_id !== Auth::id()) {
            abort(403);
        }

        $categories = $colocation->categories;
        return view('categories.index', compact('colocation', 'categories'));
    }

    /**
     * Show create category form
     */
    public function create(Colocation $colocation)
    {
        // Check authorization - owner only
        if ($colocation->owner_id !== Auth::id()) {
            abort(403);
        }

        return view('categories.create', compact('colocation'));
    }

    /**
     * Store a new category
     */
    public function store(Request $request, Colocation $colocation)
    {
        // Check authorization - owner only
        if ($colocation->owner_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $validated['colocation_id'] = $colocation->id;
        if (empty($validated['icon'])) {
            $validated['icon'] = 'tag';
        }
        if (empty($validated['color'])) {
            $validated['color'] = '#6366f1';
        }

        Category::create($validated);

        return redirect()
            ->route('categories.index', $colocation)
            ->with('status', 'Catégorie créée.');
    }

    /**
     * Edit category form
     */
    public function edit(Colocation $colocation, Category $category)
    {
        // Check authorization - owner only
        if ($colocation->owner_id !== Auth::id() || $category->colocation_id !== $colocation->id) {
            abort(403);
        }

        return view('categories.edit', compact('colocation', 'category'));
    }

    /**
     * Update category
     */
    public function update(Request $request, Colocation $colocation, Category $category)
    {
        // Check authorization - owner only
        if ($colocation->owner_id !== Auth::id() || $category->colocation_id !== $colocation->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()
            ->route('categories.index', $colocation)
            ->with('status', 'Catégorie mise à jour.');
    }

    /**
     * Delete category
     */
    public function destroy(Colocation $colocation, Category $category)
    {
        // Check authorization - owner only
        if ($colocation->owner_id !== Auth::id() || $category->colocation_id !== $colocation->id) {
            abort(403);
        }

        $category->delete();

        return redirect()
            ->route('categories.index', $colocation)
            ->with('status', 'Catégorie supprimée.');
    }
}
