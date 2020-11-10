<?php

namespace PartechGSS\ParseDates\Middleware;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Closure;

class ParseDates
{
    public $errors = [];

    private function parse($date, $field)
    {
        try {
            $date = Carbon::parse($date);
        } catch (InvalidFormatException $e) {
            $this->errors[$field] = ["The $field time must have a value that can be parsed by carbon"];
        }

        return $date;
    }

    private function getDefault($date, $default, $field)
    {
        if (isset($this->errors[$field]) && $default !== null) {
            $date = $this->parse($default, $field);
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
    public function handle($request, Closure $next, $startDefault = null, $endDefault = null)
    {
        $start = $this->parse($request->startDateTime, "startDateTime");
        $start = $this->getDefault($start, $startDefault, "startDateTime");
        $end = $this->parse($request->endDateTime, "endDateTime");
        $end = $this->getDefault($end, $endDefault, "endDateTime");

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
