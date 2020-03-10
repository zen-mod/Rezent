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

    public function getLatestActionWorkflowRun($organisation, $repository, $workflow_id): object
    {
        $url = 'https://api.github.com/repos/'
            . $organisation . '/'
            . $repository . '/'
            . 'actions/workflows/'
            . $workflow_id . '/'
            . 'runs?per_page=1';

        $response = Http::get($url)->throw();

        return json_decode($response->body())
            ->workflow_runs[0];
    }
}
