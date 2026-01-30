<?php

namespace Tests\Feature;

use App\Http\Controllers\CompanyController;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_companies(): void
    {
        Company::create([
            'name' => 'Acme Corp',
            'email' => 'info@acme.test',
            'logo' => 'logos/acme.png',
            'website' => 'https://acme.test',
        ]);

        Company::create([
            'name' => 'Globex LLC',
            'email' => 'contact@globex.test',
            'logo' => 'logos/globex.png',
            'website' => 'https://globex.test',
        ]);

        $controller = new CompanyController();
        $request = Request::create('/company', 'GET');
        $response = $controller->index($request);

        $this->assertSame(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertCount(2, $data['data']);
        $this->assertSame('Acme Corp', $data['data'][0]['name']);
    }

    public function test_index_paginates_companies(): void
    {
        Company::create([
            'name' => 'Acme Corp',
            'email' => 'info@acme.test',
            'logo' => 'logos/acme.png',
            'website' => 'https://acme.test',
        ]);

        Company::create([
            'name' => 'Globex LLC',
            'email' => 'contact@globex.test',
            'logo' => 'logos/globex.png',
            'website' => 'https://globex.test',
        ]);

        Company::create([
            'name' => 'Initech',
            'email' => 'hello@initech.test',
            'logo' => 'logos/initech.png',
            'website' => 'https://initech.test',
        ]);

        Paginator::currentPageResolver(fn () => 2);

        $controller = new CompanyController();
        $request = Request::create('/company?per_page=2&page=2', 'GET');
        $response = $controller->index($request);

        $this->assertSame(200, $response->getStatusCode());
        $data = $response->getData(true);

        $this->assertSame(2, $data['current_page']);
        $this->assertSame(3, $data['total']);
        $this->assertSame(2, $data['per_page']);
        $this->assertCount(1, $data['data']);
    }

    public function test_create_persists_company(): void
    {
        $payload = [
            'name' => 'Initech',
            'email' => 'hello@initech.test',
            'logo' => 'logos/initech.png',
            'website' => 'https://initech.test',
        ];

        $request = Request::create('/company', 'POST', $payload);
        $controller = new CompanyController();
        $response = $controller->create($request);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertDatabaseHas('company', [
            'name' => 'Initech',
            'email' => 'hello@initech.test',
        ]);
    }

    public function test_read_returns_company(): void
    {
        $company = Company::create([
            'name' => 'Umbrella',
            'email' => 'contact@umbrella.test',
            'logo' => 'logos/umbrella.png',
            'website' => 'https://umbrella.test',
        ]);

        $controller = new CompanyController();
        $response = $controller->read($company);

        $this->assertSame(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertSame('Umbrella', $data['name']);
    }

    public function test_update_modifies_company(): void
    {
        $company = Company::create([
            'name' => 'Stark Industries',
            'email' => 'hello@stark.test',
            'logo' => 'logos/stark.png',
            'website' => 'https://stark.test',
        ]);

        $payload = [
            'name' => 'Stark International',
            'email' => 'info@stark.test',
            'logo' => 'logos/stark-updated.png',
            'website' => 'https://stark.test',
        ];

        $request = Request::create('/company/' . $company->id, 'PUT', $payload);
        $controller = new CompanyController();
        $response = $controller->update($request, $company);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertDatabaseHas('company', [
            'id' => $company->id,
            'name' => 'Stark International',
            'email' => 'info@stark.test',
        ]);
    }

    public function test_delete_removes_company(): void
    {
        $company = Company::create([
            'name' => 'Wayne Enterprises',
            'email' => 'contact@wayne.test',
            'logo' => 'logos/wayne.png',
            'website' => 'https://wayne.test',
        ]);

        $controller = new CompanyController();
        $response = $controller->delete($company);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertDatabaseMissing('company', [
            'id' => $company->id,
        ]);
    }
}
