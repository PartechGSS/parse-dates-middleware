<?php

return [
    /**
     * Defaults are meant to be route specific overriding of start & end
     * Eg
     * [
     *  "/users/actions/{startDateTime}/{endDateTime}" => [
     *      "startDateTime" => "yesterday",
     *      "endDateTime" => "now"
     *  ]
     * ]
     */
    'defaults' => []
];