<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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

        $directory = '/Users/patrickcullen/Personal';
        if ($this->option('testing')) {
            // code...
            $directory = '/Users/patrickcullen/Personal/resume-libraries-tdd/storage/framework/testing/disks/projects';
        }

        // We left off with making the test pass

        info("Scanning {$directory} for projects...");
    }
}
