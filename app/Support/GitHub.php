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

        $json = json_decode($response->body())
            ->workflow_runs[0];

        if ($json->conclusion == null) {
            // Sometimes GitHub's API "conclusion" property is null, so we delay and we try again
            sleep(5);

            return $this->getLatestActionWorkflowRun($organisation, $repository, $workflow_id);
        } else {
            return $json;
        }
    }
}
