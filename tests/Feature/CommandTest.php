<?php

use Illuminate\Foundation\Application;
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
    // Given I'm running the command from a test

    // When I run the command with a test flag
    $this->artisan('app:display-libraries --testing')
        ->expectsOutput()
        ->assertSuccessful();
    // Then I get a valid output
});

// Local only, but still important that this works
test("running the command without the testing flag scans this project's parent directory", function () {
    // Given

    // When
    $this->artisan('app:display-libraries')
        ->expectsOutputToContain('Scanning /Users/patrickcullen/Personal for projects...');
    // Then
});

// This relies on local too, but I don't care right now.
// Should later be refactored to consider any machine (maybe as part of GitHub workflow)
test('running the command with the testing flag scans /storage/framework/testing/projects directory', function () {
    // Given

    // When
    $this->artisan('app:display-libraries --testing')
        ->expectsOutputToContain('Scanning /Users/patrickcullen/Personal/resume-libraries-tdd/storage/framework/testing/disks/'.(basename(dirname(Application::inferBasePath()))));
    // Then
});

// Local dependency - don't care right now
test('running the command locally detects 6 projects and excludes this one', function () {
    // Given

    // When
    $this->artisan('app:display-libraries')
        ->expectsOutputToContain('6 projects detected...');
    // Then
});

test('running the command with the test flag detects no projects by default', function () {
    // Given

    // When
    $this->artisan('app:display-libraries --testing')
        ->expectsOutputToContain('0 projects detected...');
    // Then
});

test('running the command with the test flag and adding a directory in the testing storage detects one project', function () {
    // Given
    // This makes projects/fakeProject
    Storage::fake()->makeDirectory('fakeProject');
    // When
    $this->artisan('app:display-libraries --testing')
        ->expectsOutputToContain('1 projects detected...');
    // Then
    Storage::fake()->deleteDirectory('fakeProject');
});

test('running display-libraries in an empty directory tells the user to move it', function () {
    // Given an empty projects directory

    // When we run the command
    $this->artisan('app:display-libraries --testing')
        ->expectsOutputToContain('Please install this in your projects directory.');
    // Then the command returns a suitable output
});

test('running the command in a directory with a project does not tell the user to install the app in their projects directory', function () {
    // Given
    Storage::fake()->makeDirectory('fakeProject');
    // When
    $this->artisan('app:display-libraries --testing')
        ->doesntExpectOutputToContain('Please install this in your projects directory.');
    // Then
    Storage::fake()->deleteDirectory('fakeProject');
});
