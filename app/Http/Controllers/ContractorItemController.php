<?php

namespace App\Http\Controllers;

use App\Models\ContractorItem;
use App\Models\ContractorMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractorItemController extends Controller
{
    public function index()
    {
        $items = ContractorItem::with([
            'machine.department',
        ])
            ->latest()
            ->paginate(15);

        return view(
            'contractor-items.index',
            compact('items')
        );
    }

    public function create()
    {
        $machines = ContractorMachine::with('department')
            ->orderBy('name')
            ->get();

        return view(
            'contractor-items.create',
            compact('machines')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'thumbnail' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'unit' => [
                'required',
                'string',
                'max:100',
            ],

            'rate' => [
                'required',
                'numeric',
                'min:0',
            ],

            'contractor_machine_id' => [
                'required',
                'exists:contractor_machines,id',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request
                ->file('thumbnail')
                ->store(
                    'contractor-items',
                    'public'
                );
        }

        ContractorItem::create($data);

        return redirect()
            ->route('contractor-items.index')
            ->with(
                'success',
                'Contract item created successfully.'
            );
    }

    public function show(ContractorItem $contractorItem)
    {
        $contractorItem->load([
            'machine.department',
        ]);

        return view(
            'contractor-items.show',
            compact('contractorItem')
        );
    }

    public function edit(ContractorItem $contractorItem)
    {
        $machines = ContractorMachine::with('department')
            ->orderBy('name')
            ->get();

        return view(
            'contractor-items.edit',
            compact(
                'contractorItem',
                'machines'
            )
        );
    }

    public function update(
        Request $request,
        ContractorItem $contractorItem
    ) {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'thumbnail' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'unit' => [
                'required',
                'string',
                'max:100',
            ],

            'rate' => [
                'required',
                'numeric',
                'min:0',
            ],

            'contractor_machine_id' => [
                'required',
                'exists:contractor_machines,id',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        if ($request->hasFile('thumbnail')) {
            if (
                $contractorItem->thumbnail &&
                Storage::disk('public')->exists(
                    $contractorItem->thumbnail
                )
            ) {
                Storage::disk('public')->delete(
                    $contractorItem->thumbnail
                );
            }

            $data['thumbnail'] = $request
                ->file('thumbnail')
                ->store(
                    'contractor-items',
                    'public'
                );
        }

        $contractorItem->update($data);

        return redirect()
            ->route('contractor-items.index')
            ->with(
                'success',
                'Contract item updated successfully.'
            );
    }

    public function destroy(
        ContractorItem $contractorItem
    ) {
        if (
            $contractorItem->thumbnail &&
            Storage::disk('public')->exists(
                $contractorItem->thumbnail
            )
        ) {
            Storage::disk('public')->delete(
                $contractorItem->thumbnail
            );
        }

        $contractorItem->delete();

        return redirect()
            ->route('contractor-items.index')
            ->with(
                'success',
                'Contract item deleted successfully.'
            );
    }
}
