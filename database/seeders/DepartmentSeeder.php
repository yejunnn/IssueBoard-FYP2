<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Academic Affairs, Admission & Register Office (AARO)',
            'Account & Finance Office (AFO)',
            'Asset Management & General Affairs Office (AGO)',
            'Computer Centre Office (CCO)',
            'Human Resource Office (HRO)',
            'Infrastructure Planning, Safety & Security Office (ISO)',
            'Institute of Graduate Studies & Research',
            'International Student Office',
            'Library',
            'Malaysia Chinese Literature Centre',
            'Museum & Art Gallery',
            'Planning, Development, Accreditation & Quality Assurance Office',
            'Secretarial Office',
            'Southern New Media Centre',
            'Student Affairs Office (SAO)',
            'Student Recruitment Office',
            'Student Residence Unit'
        ];

        foreach ($departments as $department) {
            Department::create(['name' => $department]);
        }
    }
}
