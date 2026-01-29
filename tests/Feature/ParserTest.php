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

test('parsing a composer.json file for dev dependencies results in an array of dev dependencies', function () {
    // Given
    // setup a Parser instance with a project root pointed at our testing fixture
    $parser = new Parser('tests/Fixtures/');

    // When
    // I call the method to parse for the dev requirements
    $devReqs = $parser->getComposerDevRequirements();

    // Then
    $expectedReqs = [
        'fakerphp/faker',
        'laravel/boost',
        'laravel/pail',
        'laravel/pint',
        'laravel/sail',
        'mockery/mockery',
        'nunomaduro/collision',
        'pestphp/pest',
        'pestphp/pest-plugin-laravel',
    ];

    expect($devReqs)->toBe($expectedReqs);
});
