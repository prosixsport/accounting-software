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

    $pictures = is_array($employee->pictures)
        ? $employee->pictures
        : (json_decode($employee->pictures ?? '[]', true) ?? []);

    $cnicPictures = is_array($employee->cnic_pictures)
        ? $employee->cnic_pictures
        : (json_decode($employee->cnic_pictures ?? '[]', true) ?? []);

    $otherDocuments = is_array($employee->other_documents)
        ? $employee->other_documents
        : (json_decode($employee->other_documents ?? '[]', true) ?? []);

    /*
    |--------------------------------------------------------------------------
    | Replace Employee Pictures
    |--------------------------------------------------------------------------
    */

    if ($request->hasFile('pictures')) {

        foreach ($pictures as $oldPicture) {
            Storage::disk('public')->delete($oldPicture);
        }

        $pictures = $this->uploadMultipleFiles(
            $request->file('pictures'),
            'employees/pictures'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Replace CNIC Pictures
    |--------------------------------------------------------------------------
    */

    if ($request->hasFile('cnic_pictures')) {

        foreach ($cnicPictures as $oldCnicPicture) {
            Storage::disk('public')->delete($oldCnicPicture);
        }

        $cnicPictures = $this->uploadMultipleFiles(
            $request->file('cnic_pictures'),
            'employees/cnic-pictures'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Replace Other Documents
    |--------------------------------------------------------------------------
    */

    if ($request->hasFile('other_documents')) {

        foreach ($otherDocuments as $oldDocument) {
            Storage::disk('public')->delete($oldDocument);
        }

        $otherDocuments = $this->uploadMultipleFiles(
            $request->file('other_documents'),
            'employees/other-documents'
        );
    }

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
        ->route('employees.show', $employee->id)
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
