<?php

namespace App\Http\Middleware;

use App\Services\RemotePolicyReader;
use Closure;

class AssertRemotePolicyAllowsAccess
{
    /** @var RemotePolicyReader */
    protected $reader;

    public function __construct(RemotePolicyReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->reader->accessIsPermitted()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Service temporarily unavailable.',
            ], 503);
        }

        return response()->view('portal.session-hold', [], 503);
    }
}
