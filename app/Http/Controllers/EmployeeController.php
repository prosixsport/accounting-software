<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
{
    $employees = Employee::latest()->get();

    $departments = Employee::query()
        ->whereNotNull('department')
        ->where('department', '!=', '')
        ->select('department')
        ->distinct()
        ->orderBy('department')
        ->pluck('department');

    $designations = Employee::query()
        ->whereNotNull('designation')
        ->where('designation', '!=', '')
        ->select('designation')
        ->distinct()
        ->orderBy('designation')
        ->pluck('designation');

    return view('employees.index', compact(
        'employees',
        'departments',
        'designations'
    ));
}

    public function create()
    {
        return view('employees.create');
    }

    private function uploadMultipleFiles($files, $folder): array
    {
        $paths = [];

        if ($files) {
            foreach ($files as $file) {
                $paths[] = $file->store($folder, 'public');
            }
        }

        return $paths;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'basic_salary' => 'required|numeric',

            'pictures.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'cnic_pictures.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'other_documents.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:8192',
        ]);

        Employee::create([
            'employee_code' => 'EMP-' . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT),
            'name' => $request->name,
            'father_name' => $request->father_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'cnic' => $request->cnic,
            'department' => $request->department,
            'designation' => $request->designation,
            'basic_salary' => $request->basic_salary,
            'joining_date' => $request->joining_date,
            'status' => $request->status ?? 'active',
            'address' => $request->address,

            'pictures' => $this->uploadMultipleFiles($request->file('pictures'), 'employees/pictures'),
            'cnic_pictures' => $this->uploadMultipleFiles($request->file('cnic_pictures'), 'employees/cnic-pictures'),
            'other_documents' => $this->uploadMultipleFiles($request->file('other_documents'), 'employees/other-documents'),
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee added successfully.');
    }

   public function show(Employee $employee)
{
    return view('employees.show', compact('employee'));
}

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required',
            'basic_salary' => 'required|numeric',

            'pictures.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'cnic_pictures.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'other_documents.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:8192',
        ]);

        $pictures = $employee->pictures ?? [];
        $cnicPictures = $employee->cnic_pictures ?? [];
        $otherDocuments = $employee->other_documents ?? [];

        $pictures = array_merge(
            $pictures,
            $this->uploadMultipleFiles($request->file('pictures'), 'employees/pictures')
        );

        $cnicPictures = array_merge(
            $cnicPictures,
            $this->uploadMultipleFiles($request->file('cnic_pictures'), 'employees/cnic-pictures')
        );

        $otherDocuments = array_merge(
            $otherDocuments,
            $this->uploadMultipleFiles($request->file('other_documents'), 'employees/other-documents')
        );

        $employee->update([
            'name' => $request->name,
            'father_name' => $request->father_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'cnic' => $request->cnic,
            'department' => $request->department,
            'designation' => $request->designation,
            'basic_salary' => $request->basic_salary,
            'joining_date' => $request->joining_date,
            'status' => $request->status ?? 'active',
            'address' => $request->address,

            'pictures' => $pictures,
            'cnic_pictures' => $cnicPictures,
            'other_documents' => $otherDocuments,
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        foreach (($employee->pictures ?? []) as $file) {
            Storage::disk('public')->delete($file);
        }

        foreach (($employee->cnic_pictures ?? []) as $file) {
            Storage::disk('public')->delete($file);
        }

        foreach (($employee->other_documents ?? []) as $file) {
            Storage::disk('public')->delete($file);
        }

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
