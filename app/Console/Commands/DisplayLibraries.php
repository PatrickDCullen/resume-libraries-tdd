<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;

use function Laravel\Prompts\info;

class DisplayLibraries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:display-libraries {--testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display libraries your projects depend on.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info('Searching projects for packages.');

        $root = $this->getDirectory();

        info("Scanning {$root} for projects...");

        $projectsDirectory = Storage::build([
            'driver' => 'local',
            'root' => $root,
        ]);

        $projectsCount = collect($projectsDirectory->directories())->filter(function ($project) {
            return $project !== basename(Application::inferBasePath());
        })->count();

        info("{$projectsCount} projects detected...");

        if ($projectsCount <= 0) {
            info('Please install this in your projects directory.');
        } else {
            // This is a perfect example of something that we can refactor because of TDD
            // Try figuring out how to get $project usable in the each() as a directory rather than a string

            $phpDependenciesFound = false;

            collect($projectsDirectory->directories())->each(function ($project) use ($projectsDirectory, &$phpDependenciesFound) {
                if ($projectsDirectory->exists("{$project}/composer.json")) {
                    $phpDependenciesFound = true;
                    info('PHP dependencies detected.');
                }
            });

            if (! $phpDependenciesFound) {
                info('No dependencies detected for this project.');
            }

            $jsDependenciesFound = false;

            collect($projectsDirectory->directories())->each(function ($project) use ($projectsDirectory, &$jsDependenciesFound) {
                if ($projectsDirectory->exists("{$project}/package.json")) {
                    $jsDependenciesFound = true;
                    info('JavaScript dependencies detected.');
                }
            });

            if (! $jsDependenciesFound) {
                info('No dependencies detected for this project.');
            }
        }

    }

    private function getDirectory()
    {
        if ($this->option('testing')) {
            // $directory = '/Users/patrickcullen/Personal/resume-libraries-tdd/storage/framework/testing/disks/Personal';
            $directory = storage_path('/framework/testing/disks/'.basename(dirname(Application::inferBasePath())));
        } else {
            // $directory = '/Users/patrickcullen/Personal';
            $directory = dirname(Application::inferBasePath());
        }

        return $directory;
    }
}
