<?php

use App\Console\Commands\DisplayLibraries;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Exception\CommandNotFoundException;

test('there is a display-libraries command', function () {
    expect(fn () => $this->artisan('app:display-libraries'))
        ->not()->toThrow(CommandNotFoundException::class);
});

test('the display-libraries command outputs something', function () {
    $this->artisan('app:display-libraries')
        ->expectsOutput()
        ->assertSuccessful();
});

test('artisan does not output the default command description', function () {
    $this->artisan('list')
        ->doesntExpectOutputToContain('Command description')
        ->assertSuccessful();
});

test('the command accepts a testing flag', function () {
    // Given base case
    // When I run the command with a test flag
    $this->artisan('app:display-libraries --testing')
        ->expectsOutput()
        ->assertSuccessful();
    // Then I get a valid output
});

// Local only, but still important that this works
test("running the command without the testing flag scans this project's parent directory", function () {
    // Given local dependency
    // When
    $this->artisan('app:display-libraries')
    // Then
        ->expectsOutputToContain('Scanning /Users/patrickcullen/Personal for projects...');
});

// This relies on local too, but I don't care right now.
// Should later be refactored to consider any machine (maybe as part of GitHub workflow)
test('running the command with the testing flag scans /storage/framework/testing/Personal directory', function () {
    // Given base case
    // When
    $this->artisan('app:display-libraries --testing')
    // Then
        ->expectsOutputToContain('Scanning /Users/patrickcullen/Personal/resume-libraries-tdd/storage/framework/testing/disks/'.(basename(dirname(Application::inferBasePath()))));
});

// Local dependency - don't care right now
test('running the command locally detects 11 projects and excludes this one', function () {
    // Given local dependency
    // When
    $this->artisan('app:display-libraries')
    // Then
        ->expectsOutputToContain('11 projects detected...');
});

test('running the command with the test flag detects no projects by default', function () {
    // Given base case
    // When
    $this->artisan('app:display-libraries --testing')
    // Then
        ->expectsOutputToContain('0 projects detected...');
});

test('running the command with the test flag and adding a directory in the testing storage detects one project', function () {
    // Given
    // This makes projects/fakeProject
    Storage::fake()->makeDirectory('fakeProject');
    // When
    $this->artisan('app:display-libraries --testing')
    // Then
        ->expectsOutputToContain('1 projects detected...');
})->after(function () {
    // Cleanup
    Storage::fake()->deleteDirectory('fakeProject');
});

test('running display-libraries in an empty directory tells the user to move it', function () {
    // Given an empty projects directory
    // When we run the command
    $this->artisan('app:display-libraries --testing')
    // Then the command returns a suitable output
        ->expectsOutputToContain('Please install this in your projects directory.');
});

test('running the command in a directory with a project does not tell the user to install the app in their projects directory', function () {
    // Given
    Storage::fake()->makeDirectory('fakeProject');
    // When
    $this->artisan('app:display-libraries --testing')
    // Then
        ->doesntExpectOutputToContain('Please install this in your projects directory.');
})->after(function () {
    // Cleanup
    Storage::fake()->deleteDirectory('fakeProject');
});

test('running the command in a directory containing an empty project tells the user no dependencies detected', function () {
    // Given
    Storage::fake()->makeDirectory('fakeProject');
    // When
    $this->artisan('app:display-libraries --testing')
    // Then
        ->expectsOutputToContain('No dependencies detected for this project.');
})->after(function () {
    // Cleanup
    Storage::fake()->deleteDirectory('fakeProject');
});

test('running the command in a directory containing a project with a composer.json detects that file', function () {
    // Given
    Storage::fake()->makeDirectory('fakeProject');
    Storage::fake()->put('fakeProject/composer.json', '', 'public');
    // When
    $this->artisan('app:display-libraries --testing')
    // Then
        ->expectsOutputToContain('PHP dependencies detected.');
})->after(function () {
    // Cleanup
    Storage::fake()->deleteDirectory('fakeProject');
});

test('running the command in a directory containing a project with a package.json detects that file', function () {
    // Given
    Storage::fake()->makeDirectory('fakeProject');
    Storage::fake()->put('fakeProject/package.json', '', 'public');
    // When
    $this->artisan('app:display-libraries --testing')
    // Then
        ->expectsOutputToContain('JavaScript dependencies detected.');
})->after(function () {
    // Cleanup
    Storage::fake()->deleteDirectory('fakeProject');
});

test('running the command detects and outputs the project name that it is scanning', function () {
    // Given
    Storage::fake()->makeDirectory('fakeProj1');
    // When
    $this->artisan('app:display-libraries --testing')
    // Then
        ->expectsOutputToContain('Scanning fakeProj1...');
})->after(function () {
    // Cleanup
    Storage::fake()->deleteDirectory('fakeProj1');
});

test('running the command in a project with no dependencies outputs one and only one null message', function () {
    // Given
    Storage::fake()->makeDirectory('emptyProject');
    // When
    // We have to use withoutMockingConsoleOutput in order to act on the output in this test
    // https://stackoverflow.com/questions/67828985/how-to-inspect-the-actual-laravel-command-output-while-writing-tests
    $this->withoutMockingConsoleOutput()->artisan('app:display-libraries --testing');
    // Then
    $output = Artisan::output();
    $nullMessageCount = substr_count($output, 'No dependencies detected for this project.');
    expect($nullMessageCount)->toBe(1);
})->after(function () {
    // Cleanup
    Storage::fake()->deleteDirectory('emptyProject');
});

// todo this might even be a different test
test('parsing a composer.json file for its dependencies returns them as an array of strings', function () {
    // Given
    $composer = file_get_contents(__DIR__.'/../Fixtures/composer.json');

    // When
    $requirements = (new DisplayLibraries)->getComposerRequirements($composer);

    // Then
    $correctRequirements = [
        'php', 'laravel/framework', 'laravel/tinker', 'livewire/flux', 'livewire/volt',
    ];
    expect($requirements)->toBe($correctRequirements);
});
