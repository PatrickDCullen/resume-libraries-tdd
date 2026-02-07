<?php

use App\Services\Sorter;

// This test alone adds 0.5s to suite when "fresh", 0.25 when cached by Packagist
// Also depends on monthy downloads of both packages
// Ideally, we would say "given this canned HTTP response, this is what we expect"
test('giving the sorter an array of dependencies outputs the array sorted by downloads, descending', function () {
    // Given
    $dependencies = ['juling/laravel-devtools', 'fakerphp/faker'];
    // When
    $sorter = new Sorter;
    $composerRequirementsByDownload = $sorter->getComposerRequirementsByDownloads($dependencies);
    // Then
    $expected = ['fakerphp/faker', 'juling/laravel-devtools'];
    expect($composerRequirementsByDownload)->toBe($expected);
});
