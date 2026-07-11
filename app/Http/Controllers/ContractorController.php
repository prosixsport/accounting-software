<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\ContractorDepartment;
use App\Models\ContractorMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class ContractorController extends Controller
{
public function index()
{
    $contractors = Contractor::with([
        'department',
        'machine',
    ])
    ->latest()
    ->paginate(10);

    return view(
        'contractors.index',
        compact('contractors')
    );
}

    public function create()
    {
        $departments = ContractorDepartment::with([
            'machines' => function ($query) {
                $query->orderBy('name');
            },
        ])
            ->orderBy('name')
            ->get();

        return view(
            'contractors.create',
            compact('departments')
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

            'cnic' => [
                'required',
                'string',
                'max:15',
                'unique:contractors,cnic',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'contractor_department_id' => [
                'required',
                'exists:contractor_departments,id',
            ],

            'contractor_machine_id' => [
                'nullable',
                'exists:contractor_machines,id',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:3072',
            ],

            'cnic_front' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'cnic_back' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'other_documents' => [
                'nullable',
                'array',
            ],

            'other_documents.*' => [
                'file',
                'mimes:jpg,jpeg,png,webp,pdf,doc,docx',
                'max:10240',
            ],
        ]);

        if (!empty($data['contractor_machine_id'])) {
            $validMachine = ContractorMachine::where(
                'id',
                $data['contractor_machine_id']
            )
                ->where(
                    'contractor_department_id',
                    $data['contractor_department_id']
                )
                ->exists();

            if (!$validMachine) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'contractor_machine_id' =>
                            'Selected machine does not belong to the selected department.',
                    ]);
            }
        }

        DB::beginTransaction();

        $uploadedPaths = [];

        try {
            if ($request->hasFile('photo')) {
                $data['photo'] = $request
                    ->file('photo')
                    ->store(
                        'contractors/photos',
                        'public'
                    );

                $uploadedPaths[] = $data['photo'];
            }

            $data['cnic_front'] = $request
                ->file('cnic_front')
                ->store(
                    'contractors/cnic',
                    'public'
                );

            $uploadedPaths[] = $data['cnic_front'];

            $data['cnic_back'] = $request
                ->file('cnic_back')
                ->store(
                    'contractors/cnic',
                    'public'
                );

            $uploadedPaths[] = $data['cnic_back'];

            $contractor = Contractor::create($data);

            if ($request->hasFile('other_documents')) {
                foreach (
                    $request->file('other_documents')
                    as $document
                ) {
                    $path = $document->store(
                        'contractors/documents',
                        'public'
                    );

                    $uploadedPaths[] = $path;

                    $contractor->documents()->create([
                        'file_name' =>
                            $document->getClientOriginalName(),

                        'file_path' => $path,

                        'mime_type' =>
                            $document->getClientMimeType(),

                        'file_size' =>
                            $document->getSize(),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('contractors.index')
                ->with(
                    'success',
                    'Contractor added successfully.'
                );
        } catch (Throwable $exception) {
            DB::rollBack();

            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'error' =>
                        'Contractor could not be saved. Please try again.',
                ]);
        }
    }

public function show(Contractor $contractor)
{
    $contractor->load([
        'department',
        'machine',
        'documents',
    ]);

    return view(
        'contractors.show',
        compact('contractor')
    );
}

    public function edit(Contractor $contractor)
    {
        $departments = ContractorDepartment::with([
            'machines' => function ($query) {
                $query->orderBy('name');
            },
        ])
            ->orderBy('name')
            ->get();

        $contractor->load('documents');

        return view(
            'contractors.edit',
            compact(
                'contractor',
                'departments'
            )
        );
    }

    public function destroy(Contractor $contractor)
    {
        $contractor->load('documents');

        $files = [
            $contractor->photo,
            $contractor->cnic_front,
            $contractor->cnic_back,
        ];

        foreach ($contractor->documents as $document) {
            $files[] = $document->file_path;
        }

        $contractor->delete();

        foreach (array_filter($files) as $file) {
            Storage::disk('public')->delete($file);
        }

        return redirect()
            ->route('contractors.index')
            ->with(
                'success',
                'Contractor deleted successfully.'
            );
    }
    public function update(
    Request $request,
    Contractor $contractor
) {
    $data = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
        ],

        'cnic' => [
            'required',
            'string',
            'max:15',
            Rule::unique('contractors', 'cnic')
                ->ignore($contractor->id),
        ],

        'phone' => [
            'required',
            'string',
            'max:20',
        ],

        'contractor_department_id' => [
            'required',
            'exists:contractor_departments,id',
        ],

        'contractor_machine_id' => [
            'nullable',
            'exists:contractor_machines,id',
        ],

        'status' => [
            'required',
            Rule::in([
                'active',
                'inactive',
            ]),
        ],

        'address' => [
            'nullable',
            'string',
        ],

        'notes' => [
            'nullable',
            'string',
        ],

        'photo' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:10240',
        ],

        'cnic_front' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:20480',
        ],

        'cnic_back' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:20480',
        ],

        'other_documents' => [
            'nullable',
            'array',
        ],

        'other_documents.*' => [
            'file',
            'mimes:jpg,jpeg,png,webp,pdf,doc,docx',
            'max:51200',
        ],
    ]);

    if (!empty($data['contractor_machine_id'])) {
        $validMachine = ContractorMachine::where(
            'id',
            $data['contractor_machine_id']
        )
            ->where(
                'contractor_department_id',
                $data['contractor_department_id']
            )
            ->exists();

        if (!$validMachine) {
            return back()
                ->withInput()
                ->withErrors([
                    'contractor_machine_id' =>
                        'Selected machine does not belong to the selected department.',
                ]);
        }
    }

    DB::beginTransaction();

    $newUploadedPaths = [];
    $oldFilesToDelete = [];

    try {
        if ($request->hasFile('photo')) {
            $newPhoto = $request
                ->file('photo')
                ->store(
                    'contractors/photos',
                    'public'
                );

            $newUploadedPaths[] = $newPhoto;

            if ($contractor->photo) {
                $oldFilesToDelete[] =
                    $contractor->photo;
            }

            $data['photo'] = $newPhoto;
        } else {
            unset($data['photo']);
        }

        if ($request->hasFile('cnic_front')) {
            $newCnicFront = $request
                ->file('cnic_front')
                ->store(
                    'contractors/cnic',
                    'public'
                );

            $newUploadedPaths[] = $newCnicFront;

            if ($contractor->cnic_front) {
                $oldFilesToDelete[] =
                    $contractor->cnic_front;
            }

            $data['cnic_front'] =
                $newCnicFront;
        } else {
            unset($data['cnic_front']);
        }

        if ($request->hasFile('cnic_back')) {
            $newCnicBack = $request
                ->file('cnic_back')
                ->store(
                    'contractors/cnic',
                    'public'
                );

            $newUploadedPaths[] = $newCnicBack;

            if ($contractor->cnic_back) {
                $oldFilesToDelete[] =
                    $contractor->cnic_back;
            }

            $data['cnic_back'] =
                $newCnicBack;
        } else {
            unset($data['cnic_back']);
        }

        $contractor->update($data);

        if ($request->hasFile('other_documents')) {
            foreach (
                $request->file('other_documents')
                as $document
            ) {
                $path = $document->store(
                    'contractors/documents',
                    'public'
                );

                $newUploadedPaths[] = $path;

                $contractor->documents()->create([
                    'file_name' =>
                        $document->getClientOriginalName(),

                    'file_path' => $path,

                    'mime_type' =>
                        $document->getClientMimeType(),

                    'file_size' =>
                        $document->getSize(),
                ]);
            }
        }

        DB::commit();

        foreach ($oldFilesToDelete as $oldFile) {
            Storage::disk('public')
                ->delete($oldFile);
        }

        return redirect()
            ->route('contractors.index')
            ->with(
                'success',
                'Contractor updated successfully.'
            );
    } catch (Throwable $exception) {
        DB::rollBack();

        foreach ($newUploadedPaths as $path) {
            Storage::disk('public')
                ->delete($path);
        }

        report($exception);

        return back()
            ->withInput()
            ->withErrors([
                'error' =>
                    'Contractor could not be updated. Please try again.',
            ]);
    }
}
}
