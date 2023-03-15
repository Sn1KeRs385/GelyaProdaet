<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\File\IndexRequest;
use App\Http\Resources\Api\V1\File\IndexResource;
use App\Models\User;
use App\Repositories\FileRepository;
use App\Utils\ResponseFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function __construct(
        protected FileRepository $fileRepository,
        protected ResponseFormatter $responseFormatter
    ) {
    }

    public function index(IndexRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $perPage = $request->per_page ?? 25;

        $files = $this->fileRepository->paginateListForUser($user, $perPage);

        return response()->json(
            $this->responseFormatter->formatPaginator($files, IndexResource::class),
            Response::HTTP_OK
        );
    }
}
