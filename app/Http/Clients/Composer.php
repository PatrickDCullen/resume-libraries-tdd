<?php

namespace App\Http\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Utils;

class Composer
{
    public $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://packagist.org/',
            'version' => '2.0',
            'headers' => [
                'user-agent' => Utils::defaultUserAgent().' '.'mailto=cullenpatrickd@gmail.com',
            ],
        ]);
    }

    public function getMonthlyDownloads($package)
    {
        $result = $this->client
            ->get("packages/{$package}.json", [])
            ->getBody()
            ->getContents();

        // Some "packages" may not be on Packagist such as php and php extensions
        return json_decode($result)?->package?->downloads?->monthly ?? 0;
    }
}
