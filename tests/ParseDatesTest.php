<?php

namespace PartehGSS\ParseDates\Tests;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use PartechGSS\ParseDates\Middleware\ParseDates;

class ParseDatesTest extends \PHPUnit\Framework\TestCase
{
    public $route = "/fleet/v1/assets/management/rented/dtcs/{startDateTime}/{endDateTime}";

    protected function getRequest($uri)
    {
        $request = new Request(
            [], [], [], [], [],
            ["REQUEST_URI" => $uri]
        );
        $route = (new Route("GET", $this->route, []))->bind($request);
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        return $request;
    }

    public function testDatesAreParsed()
    {
        $last_week = Carbon::parse("last week")->format("Y-m-d h:i:s");
        $yesterday = Carbon::parse("yesterday")->format("Y-m-d h:i:s");

        $request = $this->getRequest("/fleet/v1/assets/management/rented/dtcs/last%20week/yesterday");
        $middleware = new ParseDates;

        $middleware->handle($request, function ($req) use ($last_week, $yesterday) {
            $this->assertEquals($last_week, $req->startDateTime);
            $this->assertEquals($yesterday, $req->endDateTime);
        });
    }

    public function testInvalidDateDefaultsToNow()
    {
        $last_week = Carbon::parse("last week")->format("Y-m-d h:i:s");
        $now = Carbon::now()->format("Y-m-d h:i:s");

        $request = $this->getRequest("/fleet/v1/assets/management/rented/dtcs/last%20week/asdasdsad");
        $middleware = new ParseDates;

        $middleware->handle($request, function ($req) use ($last_week, $now) {
            $this->assertEquals($last_week, $req->startDateTime, "start times dont match");
            $this->assertEquals($now, $req->endDateTime, "end times dont match");
        });
    }
}
