<?php

namespace Database\Seeders;

use App\Models\ContractorDepartment;
use App\Models\ContractorMachine;
use Illuminate\Database\Seeder;

class ContractorDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departmentNames = [
            'Stitching',
            'Cutting',
            'Sublimation',
            'Laser',
            'Embroidery',
            'Packing',
        ];

        foreach ($departmentNames as $departmentName) {
            ContractorDepartment::firstOrCreate([
                'name' => $departmentName,
            ]);
        }

        $stitching = ContractorDepartment::where(
            'name',
            'Stitching'
        )->first();

        if ($stitching) {
            $machines = [
                'Singer',
                'Overlock',
                'Flatlock',
                'Zigzag',
                'Buttonhole (Kajj)',
                'Bartack',
            ];

            foreach ($machines as $machineName) {
                ContractorMachine::firstOrCreate([
                    'contractor_department_id' => $stitching->id,
                    'name' => $machineName,
                ]);
            }
        }
    }
}
