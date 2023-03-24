<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Api\Admin\BaseIndexRequest;
use App\Http\Resources\Api\Admin\BaseIndexResources;
use App\Services\Admin\BaseCrudService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class BaseCrudController extends BaseAdminController
{
    protected BaseCrudService $crudService;

    public function all(): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            $this->crudService->all(),
            Response::HTTP_OK
        );
    }

    public function index(BaseIndexRequest $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            BaseIndexResources::make(
                $this->crudService->index(
                    $request->page ?? 1,
                    $request->per_page ?? 25
                )
            ),
            Response::HTTP_OK
        );
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->toArray();

        return response()->json(['id' => $this->crudService->store($data)->id], Response::HTTP_OK);
    }

    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->crudService->show($id), Response::HTTP_OK);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->toArray();

        return response()->json(['id' => $this->crudService->update($id, $data)->id], Response::HTTP_OK);
    }

    public function destroy(string $id)
    {
        //
    }
}
