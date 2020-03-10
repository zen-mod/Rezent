<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class Pipelines
{
    public function getResourceDetails(string $url): object
    {
        $response = Http::get($url)->throw();

        return json_decode($response->body());
    }
}
