<?php

namespace App\Http\Clients;

use Illuminate\Support\Facades\Http;

class Npm
{
    public static function getMonthlyDownloads($package)
    {
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

        return json_decode($response->getBody()->getContents(), true)['objects'][0]['downloads']['monthly'];
    }
}
