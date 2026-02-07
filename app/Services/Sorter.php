<?php

namespace App\Services;

use App\Http\Clients\Composer;

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
}
