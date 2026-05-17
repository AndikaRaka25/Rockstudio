<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Studio;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryItem::with('studio')->orderBy('category');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        $items = $query->paginate(15);
        return view('owner.inventory.index', compact('items'));
    }

    public function create()
    {
        $studios = Studio::all();
        return view('owner.inventory.create', compact('studios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'name' => 'required|string|max:255',
            'category' => 'required|in:alat_musik,alat_rekaman,alat_elektronik',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:baik,cukup,perlu_perbaikan',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        InventoryItem::create($validated);

        return redirect()->route('owner.inventory.index')
            ->with('success', __('messages.success'));
    }

    public function edit(InventoryItem $inventory)
    {
        $studios = Studio::all();
        return view('owner.inventory.edit', compact('inventory', 'studios'));
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'name' => 'required|string|max:255',
            'category' => 'required|in:alat_musik,alat_rekaman,alat_elektronik',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:baik,cukup,perlu_perbaikan',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $inventory->update($validated);

        return redirect()->route('owner.inventory.index')
            ->with('success', __('messages.success'));
    }

    public function destroy(InventoryItem $inventory)
    {
        $inventory->delete();
        return redirect()->route('owner.inventory.index')
            ->with('success', __('messages.success'));
    }
}
