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

            return 0;
        }

        collect($projectsDirectory->directories())->each(function ($project) use ($projectsDirectory) {
            info("Scanning {$project}...");
            $hasDependencies = false;
            if ($projectsDirectory->exists("{$project}/composer.json")) {
                info('PHP dependencies detected.');
                $hasDependencies = true;
            }

            if ($projectsDirectory->exists("{$project}/package.json")) {
                info('JavaScript dependencies detected.');
                $hasDependencies = true;
            }

            if (! $hasDependencies) {
                info('No dependencies detected for this project.');
            }
        });
    }

    public function getComposerRequirements($fileContents)
    {
        return array_keys(json_decode($fileContents, true)['require']);
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
