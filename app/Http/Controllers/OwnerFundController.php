<?php

namespace App\Http\Controllers;

use App\Models\OwnerFund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OwnerFundController extends Controller
{
    public function index(Request $request)
    {
        $query = OwnerFund::query();

        if ($request->filled('from_date')) {
            $query->whereDate('fund_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('fund_date', '<=', $request->to_date);
        }

        if ($request->filled('received_in')) {
            $query->where('received_in', $request->received_in);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('owner_name', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $ownerFunds = $query
            ->latest('fund_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $totalFunds = OwnerFund::sum('amount');

        $cashFunds = OwnerFund::where('received_in', 'cash')
            ->sum('amount');

        $bankFunds = OwnerFund::where('received_in', 'bank')
            ->sum('amount');

        $thisWeekFunds = OwnerFund::whereBetween('fund_date', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])->sum('amount');

        $lastWeekFunds = OwnerFund::whereBetween('fund_date', [
            now()->subWeek()->startOfWeek(),
            now()->subWeek()->endOfWeek(),
        ])->sum('amount');

        return view('owner-funds.index', compact(
            'ownerFunds',
            'totalFunds',
            'cashFunds',
            'bankFunds',
            'thisWeekFunds',
            'lastWeekFunds'
        ));
    }

    public function create()
    {
        return view('owner-funds.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fund_date' => [
                'required',
                'date',
            ],
            'owner_name' => [
                'required',
                'string',
                'max:255',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],
            'received_in' => [
                'required',
                'in:cash,bank',
            ],
            'purpose' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'reference_number' => [
                'nullable',
                'string',
                'max:100',
            ],
            'attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:10240',
            ],
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request
                ->file('attachment')
                ->store('owner-funds', 'public');
        }

        $validated['is_active'] = true;

        OwnerFund::create($validated);

        return redirect()
            ->route('owner-funds.index')
            ->with('success', 'Owner fund added successfully.');
    }

    public function show(OwnerFund $ownerFund)
    {
        return view('owner-funds.show', compact('ownerFund'));
    }

    public function edit(OwnerFund $ownerFund)
    {
        return view('owner-funds.edit', compact('ownerFund'));
    }

    public function update(Request $request, OwnerFund $ownerFund)
    {
        $validated = $request->validate([
            'fund_date' => [
                'required',
                'date',
            ],
            'owner_name' => [
                'required',
                'string',
                'max:255',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],
            'received_in' => [
                'required',
                'in:cash,bank',
            ],
            'purpose' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'reference_number' => [
                'nullable',
                'string',
                'max:100',
            ],
            'attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:10240',
            ],
        ]);

        if ($request->hasFile('attachment')) {
            if (
                $ownerFund->attachment &&
                Storage::disk('public')->exists($ownerFund->attachment)
            ) {
                Storage::disk('public')->delete($ownerFund->attachment);
            }

            $validated['attachment'] = $request
                ->file('attachment')
                ->store('owner-funds', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $ownerFund->update($validated);

        return redirect()
            ->route('owner-funds.index')
            ->with('success', 'Owner fund updated successfully.');
    }

    public function destroy(OwnerFund $ownerFund)
    {
        if (
            $ownerFund->attachment &&
            Storage::disk('public')->exists($ownerFund->attachment)
        ) {
            Storage::disk('public')->delete($ownerFund->attachment);
        }

        $ownerFund->delete();

        return redirect()
            ->route('owner-funds.index')
            ->with('success', 'Owner fund deleted successfully.');
    }
}
