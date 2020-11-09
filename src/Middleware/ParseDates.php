<?php

namespace PartechGSS\ParseDates\Middleware;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Closure;

class ParseDates
{
    private function parseOrNow($date)
    {
        try {
            $date = Carbon::parse($date);
        } catch (InvalidFormatException $e) {
            $date = Carbon::now();
        }

        return $date;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(&$request, Closure $next)
    {
        $start = $this->parseOrNow($request->startDateTime);
        $end = $this->parseOrNow($request->endDateTime);

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
