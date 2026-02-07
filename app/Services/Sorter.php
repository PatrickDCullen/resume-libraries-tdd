<?php

namespace App\Services;

use App\Http\Clients\Composer;

class Sorter
{
    public static function sortComposerRequirementsByDownloads(array $requirements)
    {
        $client = new Composer;

        return collect($requirements)->map(function ($package) use ($client) {
            $downloads = $client->getMonthlyDownloads($package);

            return ['package' => $package, 'downloads' => $downloads];
        })->sortByDesc('downloads')
            ->values()
            ->map(function ($item) {
                return $item['package'];
            })
            ->all();
    }
}
