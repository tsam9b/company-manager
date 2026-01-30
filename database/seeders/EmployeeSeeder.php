<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        Employee::query()->delete();

        $companies = Company::query()->get(['id', 'name']);

        foreach ($companies as $company) {
            Employee::create([
                'first_name' => 'Alex',
                'last_name' => $company->name,
                'company_id' => $company->id,
                'email' => 'alex.' . $company->id . '@example.test',
                'phone' => '+1-555-010' . $company->id,
            ]);

            Employee::create([
                'first_name' => 'Jamie',
                'last_name' => $company->name,
                'company_id' => $company->id,
                'email' => 'jamie.' . $company->id . '@example.test',
                'phone' => '+1-555-020' . $company->id,
            ]);
        }
    }
}
