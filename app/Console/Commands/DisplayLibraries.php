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

        $directory = $this->getDirectory();

        info("Scanning {$directory} for projects...");

        $disk = Storage::build([
            'driver' => 'local',
            'root' => $directory,
        ]);

        $projectsCount = collect($disk->directories())->filter(function ($project) {
            return $project !== basename(Application::inferBasePath());
        })->count();

        info("{$projectsCount} projects detected...");
    }

    private function getDirectory()
    {
        if ($this->option('testing')) {
            // $directory = '/Users/patrickcullen/Personal/resume-libraries-tdd/storage/framework/testing/disks/projects';
            $directory = storage_path('/framework/testing/disks/projects');
        } else {
            // $directory = '/Users/patrickcullen/Personal';
            $directory = dirname(Application::inferBasePath());
        }

        return $directory;
    }
}
