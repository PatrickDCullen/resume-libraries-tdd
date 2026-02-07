<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Utils;

class Sorter
{
    public function getComposerRequirementsByDownloads(array $requirements)
    {
        return collect($requirements)->map(function ($req) {
            $client = new Client([
                'base_uri' => 'https://packagist.org/',
                'version' => '2.0',
                'headers' => [
                    'user-agent' => Utils::defaultUserAgent().' '.'mailto=cullenpatrickd@gmail.com',
                ],
            ]);

            $result = $client
                ->get("packages/{$req}.json", [])
                ->getBody()
                ->getContents();

            $downloads = json_decode($result)->package->downloads->monthly;

            return ['package' => $req, 'downloads' => $downloads];
        })->sortByDesc('downloads')
            ->values()
            ->map(function ($item) {
                return $item['package'];
            })
            ->all();
    }
}
