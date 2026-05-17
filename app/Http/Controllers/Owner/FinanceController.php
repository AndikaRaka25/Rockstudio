<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Studio;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('booking')->orderByDesc('date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('studio_id')) {
            $query->where('studio_id', $request->studio_id);
        }

        $transactions = $query->paginate(15);
        $studios = Studio::all();

        $totalIncome = Transaction::income()->sum('amount');
        $totalExpense = Transaction::expense()->sum('amount');

        return view('owner.finance.index', compact('transactions', 'studios', 'totalIncome', 'totalExpense'));
    }

    public function create()
    {
        $studios = Studio::all();
        return view('owner.finance.create', compact('studios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
            'description' => 'required|string|max:500',
            'date' => 'required|date',
        ]);

        Transaction::create($validated);

        return redirect()->route('owner.finance.index')
            ->with('success', __('messages.success'));
    }

    public function edit(Transaction $finance)
    {
        $studios = Studio::all();
        return view('owner.finance.edit', compact('finance', 'studios'));
    }

    public function update(Request $request, Transaction $finance)
    {
        $validated = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
            'description' => 'required|string|max:500',
            'date' => 'required|date',
        ]);

        $finance->update($validated);

        return redirect()->route('owner.finance.index')
            ->with('success', __('messages.success'));
    }

    public function destroy(Transaction $finance)
    {
        $finance->delete();
        return redirect()->route('owner.finance.index')
            ->with('success', __('messages.success'));
    }
}
