<?php

use App\Services\Sorter;
use Illuminate\Support\Facades\Http;

// This test alone adds 0.5s to suite when HTTP requests are "fresh", 0.25 when cached by Packagist
// (actually half that when client is instantiated outside of loop)
// Has an dependency on the actual downloads count of both projects which is subject to change
// Both issues can be fixed by refactoring Sorter to use the HTTP facade and stubbing responses like below
test('giving the sorter an array of dependencies outputs the array sorted by downloads, descending', function () {
    // Given
    $dependencies = ['juling/laravel-devtools', 'fakerphp/faker'];
    // When
    $composerRequirementsByDownload = (new Sorter)->sortComposerRequirementsByDownloads($dependencies);
    // Then
    $expected = ['fakerphp/faker', 'juling/laravel-devtools'];
    expect($composerRequirementsByDownload)->toBe($expected);
});

// figure out how to run "integration" tests outside? (or maybe, for now just a way to manually run them once when merging)

test('the sorter can take an array of JavaScript dependencies and sort it by downloads, descending', function () {
    // Given
    $npmDependencies = ['react-native', 'axios', 'tailwindcss', 'vite'];

    // When
    Http::preventStrayRequests();
    Http::fake([
        // We stub out JSON HTTP response in the shape we expect from NPM Registry endpoints...
        'https://registry.npmjs.org/*' => Http::sequence()
            ->push(['objects' => [['downloads' => ['monthly' => 500]]]])
            ->push(['objects' => [['downloads' => ['monthly' => 499]]]])
            ->push(['objects' => [['downloads' => ['monthly' => 3]]]])
            ->push(['objects' => [['downloads' => ['monthly' => 50000]]]]),
    ]);
    // And we run the sorter's sortNpmRequirementsByDownloads() method
    $sortedDependencies = (new Sorter)->sortNpmRequirementsByDownloads($npmDependencies);

    // Then
    // we expect the sorter array to be in the order that corresponds to
    // our libraries and the canned responses from the NPM endpoint
    expect($sortedDependencies)->toBe(['vite', 'react-native', 'axios', 'tailwindcss']);
});
