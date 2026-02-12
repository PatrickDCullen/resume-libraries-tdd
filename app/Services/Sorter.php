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
            // (Speed) Does this create an HTTP client each map iteration?
            // Or is it created already (or once per app life cycle when Http::get() is called?)
            $response = Http::withOptions([
                // Keeping for reference, great to have for debugging
                // 'debug' => true,
                ['version' => '2.0'],
            ])->retry(5, function ($try, $response) {
                // Linear backoff - for each time we try a given request,
                // add 200ms up to waiting a full second on the fifth and final time
                // We do this because NPM API is not transparent at all about what they expect
                // and all retry-after headers are 0 on 429 Too Many Requests responses
                return $try * 200;
            })->get($url, $query);

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
