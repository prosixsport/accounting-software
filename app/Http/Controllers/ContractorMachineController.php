<?php

namespace App\Http\Controllers;

use App\Models\ContractorMachine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContractorMachineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'department_id' => [
                'required',
                'exists:contractor_departments,id',
            ],
        ]);

        $machines = ContractorMachine::where(
            'contractor_department_id',
            $request->department_id
        )
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'machines' => $machines,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contractor_department_id' => [
                'required',
                'exists:contractor_departments,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'contractor_machines',
                    'name'
                )->where(function ($query) use ($request) {
                    return $query->where(
                        'contractor_department_id',
                        $request->contractor_department_id
                    );
                }),
            ],
        ]);

        $machine = ContractorMachine::create([
            'contractor_department_id' =>
                $data['contractor_department_id'],
            'name' => trim($data['name']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Machine added successfully.',
            'machine' => $machine,
        ]);
    }

    public function update(
        Request $request,
        ContractorMachine $machine
    ): JsonResponse {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'contractor_machines',
                    'name'
                )
                    ->where(function ($query) use ($machine) {
                        return $query->where(
                            'contractor_department_id',
                            $machine->contractor_department_id
                        );
                    })
                    ->ignore($machine->id),
            ],
        ]);

        $machine->update([
            'name' => trim($data['name']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Machine updated successfully.',
            'machine' => $machine,
        ]);
    }

    public function destroy(
        ContractorMachine $machine
    ): JsonResponse {
        if ($machine->contractors()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This machine is being used by a contractor.',
            ], 422);
        }

        $machine->delete();

        return response()->json([
            'success' => true,
            'message' => 'Machine deleted successfully.',
        ]);
    }
}
