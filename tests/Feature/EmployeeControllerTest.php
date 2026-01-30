<?php

namespace Tests\Feature;

use App\Http\Controllers\EmployeeController;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Tests\TestCase;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function seedCompany(): Company
    {
        return Company::create([
            'name' => 'Acme Corp',
            'email' => 'info@acme.test',
            'logo' => 'logos/acme.png',
            'website' => 'https://acme.test',
        ]);
    }

    public function test_index_returns_employees(): void
    {
        $company = $this->seedCompany();

        Employee::create([
            'first_name' => 'Alex',
            'last_name' => 'Doe',
            'company_id' => $company->id,
            'email' => 'alex@example.test',
            'phone' => '+1-555-0101',
        ]);

        Employee::create([
            'first_name' => 'Jamie',
            'last_name' => 'Roe',
            'company_id' => $company->id,
            'email' => 'jamie@example.test',
            'phone' => '+1-555-0102',
        ]);

        $controller = new EmployeeController();
        $request = Request::create('/employee', 'GET');
        $response = $controller->index($request);

        $this->assertSame(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertCount(2, $data['data']);
        $this->assertSame('Alex', $data['data'][0]['first_name']);
    }

    public function test_index_paginates_employees(): void
    {
        $company = $this->seedCompany();

        Employee::create([
            'first_name' => 'Alex',
            'last_name' => 'Doe',
            'company_id' => $company->id,
            'email' => 'alex@example.test',
            'phone' => '+1-555-0101',
        ]);

        Employee::create([
            'first_name' => 'Jamie',
            'last_name' => 'Roe',
            'company_id' => $company->id,
            'email' => 'jamie@example.test',
            'phone' => '+1-555-0102',
        ]);

        Employee::create([
            'first_name' => 'Pat',
            'last_name' => 'Smith',
            'company_id' => $company->id,
            'email' => 'pat@example.test',
            'phone' => '+1-555-0103',
        ]);

        Paginator::currentPageResolver(fn () => 2);

        $controller = new EmployeeController();
        $request = Request::create('/employee?per_page=2&page=2', 'GET');
        $response = $controller->index($request);

        $this->assertSame(200, $response->getStatusCode());
        $data = $response->getData(true);

        $this->assertSame(2, $data['current_page']);
        $this->assertSame(3, $data['total']);
        $this->assertSame(2, $data['per_page']);
        $this->assertCount(1, $data['data']);
    }

    public function test_create_persists_employee(): void
    {
        $company = $this->seedCompany();

        $payload = [
            'first_name' => 'Chris',
            'last_name' => 'Evans',
            'company_id' => $company->id,
            'email' => 'chris@example.test',
            'phone' => '+1-555-0110',
        ];

        $request = Request::create('/employee', 'POST', $payload);
        $controller = new EmployeeController();
        $response = $controller->create($request);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertDatabaseHas('employee', [
            'first_name' => 'Chris',
            'last_name' => 'Evans',
            'company_id' => $company->id,
        ]);
    }

    public function test_read_returns_employee(): void
    {
        $company = $this->seedCompany();

        $employee = Employee::create([
            'first_name' => 'Taylor',
            'last_name' => 'King',
            'company_id' => $company->id,
            'email' => 'taylor@example.test',
            'phone' => '+1-555-0111',
        ]);

        $controller = new EmployeeController();
        $response = $controller->read($employee);

        $this->assertSame(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertSame('Taylor', $data['first_name']);
    }

    public function test_update_modifies_employee(): void
    {
        $company = $this->seedCompany();

        $employee = Employee::create([
            'first_name' => 'Morgan',
            'last_name' => 'Lee',
            'company_id' => $company->id,
            'email' => 'morgan@example.test',
            'phone' => '+1-555-0112',
        ]);

        $payload = [
            'first_name' => 'Morgan',
            'last_name' => 'Lee-Smith',
            'company_id' => $company->id,
            'email' => 'morgan.smith@example.test',
            'phone' => '+1-555-0222',
        ];

        $request = Request::create('/employee/' . $employee->id, 'PUT', $payload);
        $controller = new EmployeeController();
        $response = $controller->update($request, $employee);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertDatabaseHas('employee', [
            'id' => $employee->id,
            'last_name' => 'Lee-Smith',
            'email' => 'morgan.smith@example.test',
        ]);
    }

    public function test_delete_removes_employee(): void
    {
        $company = $this->seedCompany();

        $employee = Employee::create([
            'first_name' => 'Jordan',
            'last_name' => 'Miles',
            'company_id' => $company->id,
            'email' => 'jordan@example.test',
            'phone' => '+1-555-0113',
        ]);

        $controller = new EmployeeController();
        $response = $controller->delete($employee);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertDatabaseMissing('employee', [
            'id' => $employee->id,
        ]);
    }
}
