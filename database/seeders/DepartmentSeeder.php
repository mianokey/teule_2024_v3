<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Administration', 'code' => 'ADMIN'],
            ['name' => 'Finance', 'code' => 'FIN'],
            ['name' => 'Human Resources', 'code' => 'HR'],
            ['name' => 'Teule Leadership Academy', 'code' => 'TLA'],
            ['name' => 'Social Work', 'code' => 'SW'],
            ['name' => 'Programs', 'code' => 'PROG'],
            ['name' => 'Stores', 'code' => 'STORES'],
            ['name' => 'Resource Mobilization', 'code' => 'RM'],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['code' => $department['code']],
                $department
            );
        }
    }
}