<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class GitHub
{
    public function getCommitDetails(string $orgAndRepo, string $sha): object
    {
        $response = Http::get(
            'https://api.github.com/repos/'
                . $orgAndRepo
                . '/commits/'
                . $sha
        )->throw();

        return json_decode($response->body());
    }
}
