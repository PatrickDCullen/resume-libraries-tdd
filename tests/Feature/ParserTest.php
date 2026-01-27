<?php

use App\Services\Parser;

test('parsing a composer.json file for its dependencies returns them as an array of strings', function () {
    // Given
    // This is the directory of the project - where we can find our composer.json file
    $projectRoot = (__DIR__.'/../Fixtures/');

    // When
    $parser = (new Parser($projectRoot));
    $requirements = $parser->getComposerRequirements();

    // Then
    $correctRequirements = [
        'php', 'laravel/framework', 'laravel/tinker', 'livewire/flux', 'livewire/volt',
    ];
    expect($requirements)->toBe($correctRequirements);
});
