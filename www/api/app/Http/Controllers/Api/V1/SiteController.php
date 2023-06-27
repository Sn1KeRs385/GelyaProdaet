<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Site\Index\SiteIndexResource;
use App\Services\V1\SiteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SiteController extends Controller
{
    public function __construct(protected SiteService $siteService)
    {
    }

    public function indexPage(): JsonResponse
    {
        return Cache::tags(config('cache.config.api.v1.site.tag'))
            ->remember(
                config('cache.config.api.v1.site.index_page.key'),
                config('cache.config.api.v1.site.index_page.ttl'),
                function () {
                    $data = $this->siteService->getIndexPageData();

                    return response()->json(SiteIndexResource::make($data), Response::HTTP_OK);
                }
            );
    }
}
