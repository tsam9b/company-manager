<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends AbstractController
{
    public $class = Employee::class;

    public function index(Request $request = null): Response|\Illuminate\Http\JsonResponse
    {
        if ($request instanceof Request) {
            return parent::list($request);
        }

        return Inertia::render('Employee/Index');
    }

    public function create(Request $request = null): Response|\Illuminate\Http\JsonResponse
    {
        if ($request instanceof Request && $request->isMethod('POST')) {
            $validated = array_intersect_key($request->all(), array_flip((new Employee())->getFillable()));
            $employee = Employee::create($validated);

            return response()->json($employee, 201);
        }

        return Inertia::render('Employee/Create');
    }

    public function show(Employee $employee): Response
    {
        $employee->load('getCompany');

        return Inertia::render('Employee/Show', [
            'employee' => $employee,
        ]);
    }

    public function edit(Employee $employee): Response
    {
        $employee->load('getCompany');

        return Inertia::render('Employee/Edit', [
            'employee' => $employee,
        ]);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $employee = Employee::create($validated);

        return response()->json($employee, 201);
    }

    public function update(Request $request, $data): JsonResponse
    {
        if (method_exists($request, 'validated')) {
            $validated = $request->validated();
        } else {
            $validated = array_intersect_key($request->all(), array_flip((new Employee())->getFillable()));
        }

        $employee = $data instanceof Employee ? $data : Employee::findOrFail($data);
        $employee->fill($validated);
        $employee->save();

        return response()->json($employee);
    }

    public function list(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? $perPage : 10;

        $sortBy = $request->query('sort_by');
        $sortDir = strtolower((string) $request->query('sort_dir', 'asc'));
        $sortDir = in_array($sortDir, ['asc', 'desc'], true) ? $sortDir : 'asc';

        $query = $this->class::query()->with('getCompany');

        if (is_string($sortBy) && $sortBy !== '') {
            $model = new $this->class();
            $allowed = array_unique(array_merge(
                ['id', 'created_at', 'updated_at'],
                $model->getFillable()
            ));

            if (in_array($sortBy, $allowed, true)) {
                $query->orderBy($sortBy, $sortDir);
            }
        }

        $data = $query->paginate($perPage);

        return response()->json($data);
    }
}
