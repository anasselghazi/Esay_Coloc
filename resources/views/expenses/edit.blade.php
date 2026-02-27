@extends('layouts.app')

@section('title', 'Modifier une dépense')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div>
        <a href="{{ route('expenses.index', $colocation) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">← Retour</a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Modifier la dépense</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('expenses.update', [$colocation, $expense]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">Titre</label>
                <input type="text" name="title" id="title" value="{{ old('title', $expense->title) }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500">
                @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="amount" class="block text-sm font-semibold text-gray-900 mb-2">Montant (€)</label>
                <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount', $expense->amount) }}" required
                       class="w-full px-4 py-3 border border_gray-300 rounded-lg focus:ring-indigo-500">
                @error('amount')<p class="text-red-600 text_sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="expense_date" class="block text-sm font-semibold text_gray-900 mb-2">Date</label>
                <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500">
                @error('expense_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="category_id" class="block text-sm font-semibold text-gray-900 mb-2">Catégorie</label>
                <select name="category_id" id="category_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500">
                    <option value="">-- Aucune --</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">Description (optionnelle)</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500">{{ old('description', $expense->description) }}</textarea>
                @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-4 pt-4">
                <a href="{{ route('expenses.index', $colocation) }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Annuler</a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection