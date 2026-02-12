<?php

namespace App\Services;

use App\Http\Clients\Composer;
use App\Http\Clients\Npm;
use Illuminate\Support\Facades\Cache;

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
            $downloads = Cache::get($package);

            if (is_null($downloads)) {
                $downloads = $this->client->getMonthlyDownloads($package);
                Cache::put($package, $downloads);
            }

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
            $downloads = Cache::get('npm'.$package);

            if (is_null($downloads)) {
                $downloads = Npm::getMonthlyDownloads($package);
                Cache::put('npm'.$package, $downloads);
            }

            return ['package' => $package, 'downloads' => $downloads];
        })->sortByDesc('downloads')
            ->values()
            ->map(function ($item) {
                return $item['package'];
            })
            ->all();
    }
}
