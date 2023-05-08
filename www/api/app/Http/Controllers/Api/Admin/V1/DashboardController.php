<?php

namespace App\Http\Controllers\Api\Admin\V1;

use App\Http\Controllers\Api\Admin\BaseAdminController;
use App\Http\Requests\Api\Admin\V1\Dashboard\GetMainDashboardRequest;
use App\Services\Admin\V1\DashboardService;
use Carbon\Carbon;

class DashboardController extends BaseAdminController
{
    public function __construct(protected DashboardService $service)
    {
    }

    public function getMainDashboard(GetMainDashboardRequest $request): \Illuminate\Http\JsonResponse
    {
        $from = $request->from ? Carbon::createFromTimestamp($request->from) : null;
        $to = $request->to ? Carbon::createFromTimestamp($request->to) : null;

        $data = $this->service->getMainDashboard($from, $to);

        return response()->json($data);
    }
}
