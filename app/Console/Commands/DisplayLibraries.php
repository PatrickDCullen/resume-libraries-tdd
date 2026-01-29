<?php

namespace App\Console\Commands;

use App\Services\Parser;
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

        collect($projectsDirectory->directories())->each(function ($project) use ($projectsDirectory, $root) {
            info("Scanning {$project}...");

            if (! $projectsDirectory->exists("{$project}/composer.json") && ! $projectsDirectory->exists("{$project}/package.json")) {
                info('No dependencies detected for this project.');
            }

            $parser = new Parser("{$root}/{$project}/");

            if ($projectsDirectory->exists("{$project}/composer.json")) {
                info('PHP dependencies detected.');
                $this->printComposerRequirements($parser);
                $this->printComposerDevRequirements($parser);
            }

            if ($projectsDirectory->exists("{$project}/package.json")) {
                info('JavaScript dependencies detected.');
                $this->printNpmRequirements($parser);
                $this->printNpmDevRequirements($parser);
            }
        });
    }

    private function printComposerRequirements($parser)
    {
        $parser->getComposerRequirements() === [] ?: info(implode(', ', $parser->getComposerRequirements()));
    }

    private function printComposerDevRequirements($parser)
    {
        $parser->getComposerDevRequirements() === [] ?: info(implode(', ', $parser->getComposerDevRequirements()));
    }

    private function printNpmRequirements($parser)
    {
        $parser->getNpmRequirements() === [] ?: info(implode(', ', $parser->getNpmRequirements()));
    }

    private function printNpmDevRequirements($parser)
    {
        $parser->getNpmDevRequirements() === [] ?: info(implode(', ', $parser->getNpmDevRequirements()));
    }

    private function getDirectory()
    {
        // $directory = '/Users/patrickcullen/Personal';
        $directory = dirname(Application::inferBasePath());

        if ($this->option('testing')) {
            // $directory = '/Users/patrickcullen/Personal/resume-libraries-tdd/storage/framework/testing/disks/Personal';
            $directory = storage_path('/framework/testing/disks/'.basename($directory));
        }

        return $directory;
    }
}
