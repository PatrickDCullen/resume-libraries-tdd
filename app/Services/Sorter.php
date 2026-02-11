<?php

namespace App\Services;

use App\Http\Clients\Composer;
use Illuminate\Support\Facades\Http;

class Sorter
{
    protected $client;

    public function __construct()
    {
        $this->client = new Composer;
    }

    public function sortComposerRequirementsByDownloads(array $requirements)
    {
        return collect($requirements)->map(function ($package) {
            $downloads = $this->client->getMonthlyDownloads($package);

            return ['package' => $package, 'downloads' => $downloads];
        })->sortByDesc('downloads')
            ->values()
            ->map(function ($item) {
                return $item['package'];
            })
            ->all();
    }

    public function sortNpmRequirementsByDownloads(array $requirements)
    {
        return collect($requirements)->map(function ($package) {
            $url = 'https://registry.npmjs.org/-/v1/search';
            $query = ['text' => $package, 'size' => 1];
            // Does this create an HTTP client each time?
            // Or is it created already (or once per app life cycle when Http::get() is called?)
            // TODO see https://medium.com/@harrisrafto/simplifying-api-interactions-with-laravels-http-client-9d1b432035b5
            // for guzzle options (debug to answer my question)
            // and macros, which appear to be the equivalent to client like I was using above
            $response = Http::get($url, $query);
            $downloads = json_decode($response->getBody()->getContents(), true)['objects'][0]['downloads']['monthly'];

            return ['package' => $package, 'downloads' => $downloads];
        })->sortByDesc('downloads')
            ->values()
            ->map(function ($item) {
                return $item['package'];
            })
            ->all();
    }
}
