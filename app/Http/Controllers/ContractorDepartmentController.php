<?php

namespace App\Http\Controllers;

use App\Models\ContractorDepartment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContractorDepartmentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:contractor_departments,name',
            ],
        ]);

        $department = ContractorDepartment::create([
            'name' => trim($data['name']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Department added successfully.',
            'department' => $department,
        ]);
    }

    public function update(
        Request $request,
        ContractorDepartment $department
    ): JsonResponse {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'contractor_departments',
                    'name'
                )->ignore($department->id),
            ],
        ]);

        $department->update([
            'name' => trim($data['name']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully.',
            'department' => $department,
        ]);
    }

    public function destroy(
        ContractorDepartment $department
    ): JsonResponse {
        if ($department->contractors()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This department is being used by a contractor.',
            ], 422);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully.',
        ]);
    }
}
