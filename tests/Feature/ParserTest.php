<?php

use App\Services\Parser;

test('parsing a composer.json file for its dependencies returns them as an array of strings', function () {
    // Given
    $composer = file_get_contents(__DIR__.'/../Fixtures/composer.json');

    // When
    $requirements = Parser::getComposerRequirements($composer);

    // Then
    $correctRequirements = [
        'php', 'laravel/framework', 'laravel/tinker', 'livewire/flux', 'livewire/volt',
    ];
    expect($requirements)->toBe($correctRequirements);
});
