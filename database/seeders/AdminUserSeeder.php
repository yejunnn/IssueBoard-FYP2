<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->update([
                'is_admin' => true,
                'department_id' => null
            ]);
        }
    }
}
