<?php

namespace PartechGSS\ParseDates\Middleware;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Closure;

class ParseDates
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(&$request, Closure $next)
    {
        try {
            $start = Carbon::parse($request->startDateTime);
        } catch (InvalidFormatException $e) {
            $start = Carbon::now();
        }

        try {
            $end = Carbon::parse($request->endDateTime);
        } catch (InvalidFormatException $e) {
            $end = Carbon::now();
        }

        $request->route()->setParameter(
            "startDateTime",
            $start->format("Y-m-d h:i:s")
        );

        $request->route()->setParameter(
            "endDateTime",
            $end->format("Y-m-d h:i:s")
        );
        
        return $next($request);
    }
}
