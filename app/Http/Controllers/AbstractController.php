<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AbstractController extends Controller
{
    public $class;

    public function list(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? $perPage : 10;

        $sortBy = $request->query('sort_by');
        $sortDir = strtolower((string) $request->query('sort_dir', 'asc'));
        $sortDir = in_array($sortDir, ['asc', 'desc'], true) ? $sortDir : 'asc';

        $query = $this->class::query();

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



    public function read($data): JsonResponse
    {
        $model = $this->resolveModel($data);

        return response()->json($model);
    }


    public function destroy($data): JsonResponse
    {
        $data = $this->resolveModel($data);
        $data->delete();

        return response()->json(['deleted' => true]);
    }

    public function delete($data): JsonResponse
    {
        return $this->destroy($data);
    }

    protected function resolveModel($data): Model
    {
        if ($data instanceof Model) {
            return $data;
        }

        return $this->class::query()->findOrFail($data);
    }
}
