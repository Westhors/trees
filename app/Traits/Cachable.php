<?php

namespace App\Traits;

use App\Helpers\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Cache;

trait Cachable
{
    public function cacheData($cacheKeyPrefix, $queryBuilder, $cacheTime = 20)
    {
        try {
            $cacheKey = md5(serialize(request()?->fullUrl()) . $cacheKeyPrefix);

            if (Cache::has($cacheKey)) {
                // If cached, return the cached response
                return Cache::get($cacheKey);
            }

            // If not cached, generate the response
            $data = $queryBuilder->where('active', 1)->get();
            Cache::add($cacheKeyPrefix . '_' . $cacheKey, $data, $cacheTime);
            return $data;
        } catch (Exception $e) {
            // Log any exceptions if needed
            return JsonResponse::respondError($e->getMessage());

        }
    }
}
