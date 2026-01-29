<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Electrical' => 'Infrastructure Planning, Safety & Security Office (ISO)',
            'Plumbing' => 'Infrastructure Planning, Safety & Security Office (ISO)',
            'HVAC' => 'Infrastructure Planning, Safety & Security Office (ISO)',
            'Lighting' => 'Infrastructure Planning, Safety & Security Office (ISO)',
            'Security' => 'Infrastructure Planning, Safety & Security Office (ISO)',
            'Safety' => 'Infrastructure Planning, Safety & Security Office (ISO)',
            
            'IT Equipment' => 'Computer Centre Office (CCO)',
            'Network' => 'Computer Centre Office (CCO)',
            'Software' => 'Computer Centre Office (CCO)',
            'Internet' => 'Computer Centre Office (CCO)',
            'Computer Lab' => 'Computer Centre Office (CCO)',
            
            'Cleanliness' => 'Asset Management & General Affairs Office (AGO)',
            'Maintenance' => 'Asset Management & General Affairs Office (AGO)',
            'Furniture' => 'Asset Management & General Affairs Office (AGO)',
            'Pest Control' => 'Asset Management & General Affairs Office (AGO)',
            'Waste Management' => 'Asset Management & General Affairs Office (AGO)',
            
            'Student Services' => 'Student Affairs Office (SAO)',
            'Student Activities' => 'Student Affairs Office (SAO)',
            'Student Welfare' => 'Student Affairs Office (SAO)',
            
            'Library Services' => 'Library',
            'Library Equipment' => 'Library',
            'Library Resources' => 'Library',
            
            'Student Housing' => 'Student Residence Unit',
            'Dormitory' => 'Student Residence Unit',
            'Residence Maintenance' => 'Student Residence Unit',
            
            'Academic Services' => 'Academic Affairs, Admission & Register Office (AARO)',
            'Registration' => 'Academic Affairs, Admission & Register Office (AARO)',
            'Admission' => 'Academic Affairs, Admission & Register Office (AARO)',
            
            'Financial Services' => 'Account & Finance Office (AFO)',
            'Billing' => 'Account & Finance Office (AFO)',
            'Payment' => 'Account & Finance Office (AFO)',
            
            'HR Services' => 'Human Resource Office (HRO)',
            'Staff Services' => 'Human Resource Office (HRO)',
            
            'International Services' => 'International Student Office',
            'Visa Services' => 'International Student Office',
            
            'Museum Services' => 'Museum & Art Gallery',
            'Art Gallery' => 'Museum & Art Gallery',
            
            'Media Services' => 'Southern New Media Centre',
            'Broadcasting' => 'Southern New Media Centre',
            
            'General' => 'Asset Management & General Affairs Office (AGO)',
            'Other' => 'Asset Management & General Affairs Office (AGO)',
        ];

        foreach ($categories as $categoryName => $departmentName) {
            $department = Department::where('name', $departmentName)->first();
            if ($department) {
                Category::create([
                    'name' => $categoryName,
                    'department_id' => $department->id
                ]);
            }
        }
    }
}
