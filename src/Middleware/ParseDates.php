<?php

namespace PartechGSS\ParseDates\Middleware;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Closure;
use Illuminate\Support\Facades\Config;

class ParseDates
{
    private $defaults;
    public $errors = [];

    public function __construct()
    {
        $this->defaults = Config::get("dates.defaults", []);
    }

    private function parse($date, $field)
    {
        try {
            $date = Carbon::parse($date);
        } catch (InvalidFormatException $e) {
            $this->errors[$field] = ["The $field time must have a value that can be parsed by carbon"];
        }

        return $date;
    }

    private function getDefault($uri, $date, $field)
    {
        if (isset($this->defaults[$uri][$field], $this->errors[$field])) {
            $date = $this->parse($this->defaults[$uri][$field], $field);
            unset($this->errors[$field]);
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
        $start = $this->parse($request->startDateTime, "startDateTime");
        $end = $this->parse($request->endDateTime, "endDateTime");

        $start = $this->getDefault($request->route()->uri(), $start, "startDateTime");
        $end = $this->getDefault($request->route()->uri(), $end, "endDateTime");

        if (count($this->errors) > 0) {
            abort(422, json_encode(["errors" => $this->errors]));
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
