<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class RemotePolicyReader
{
   
    public function accessIsPermitted(): bool
    {
        $host = config('services.remote_policy.host');
        $key = config('services.remote_policy.key');

        if ($host === '' || $key === '') {
            return true;
        }

        $cacheKey = config('services.remote_policy.cache_key');
        $ttl = (int) config('services.remote_policy.cache_ttl', 300);
        $resource = config('services.remote_policy.resource', 'app_kill_switch');
        $endpoint = $host.'/rest/v1/'.$resource;

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'apikey' => $key,
                    'Authorization' => 'Bearer '.$key,
                    'Accept' => 'application/json',
                ])
                ->get($endpoint, [
                    'id' => 'eq.1',
                    'select' => 'enabled',
                ]);

            if (!$response->successful()) {
                return $this->resolveFromCache($cacheKey, true);
            }

            $rows = $response->json();
            if (!is_array($rows) || $rows === []) {
                return true;
            }

            $enabled = (bool) ($rows[0]['enabled'] ?? true);
            Cache::put($cacheKey, $enabled, $ttl);

            return $enabled;
        } catch (Throwable $e) {
            return $this->resolveFromCache($cacheKey, true);
        }
    }

    protected function resolveFromCache(string $cacheKey, bool $defaultIfMissing): bool
    {
        if (!Cache::has($cacheKey)) {
            return $defaultIfMissing;
        }

        return (bool) Cache::get($cacheKey);
    }
}
