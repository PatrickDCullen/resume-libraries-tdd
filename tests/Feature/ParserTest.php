<?php

use App\Services\Parser;

test('parsing a composer.json file for its dependencies returns them as an accurate array of strings', function () {
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

test('parsing a composer.json file for dev dependencies results in the correct array of dev dependencies', function () {
    // Given
    // setup a Parser instance with a project root pointed at our testing fixture
    $parser = new Parser('tests/Fixtures/');

    // When
    // I call the method to parse for the dev requirements
    $devReqs = $parser->getComposerDevRequirements();

    // Then
    $expectedDevReqs = [
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

    expect($devReqs)->toBe($expectedDevReqs);
});

test('parsing a package.json file for dependencies results in the correct array of dependencies', function () {
    // Given
    $parser = new Parser('tests/Fixtures/');

    // When
    $requirements = $parser->getNpmRequirements();

    // Then
    $expectedReqs = [
        '@inertiajs/vue3',
        '@vueuse/core',
        'class-variance-authority',
        'clsx',
        'laravel-vite-plugin',
        'lucide-vue-next',
        'reka-ui',
        'tailwind-merge',
        'tailwindcss',
        'tw-animate-css',
        'vue',
    ];

    expect($requirements)->toBe($expectedReqs);
});

test('parsing a package.json file for dev dependencies results in the correct array of dev dependencies', function () {
    // Given
    $parser = new Parser('tests/Fixtures/');

    // When
    $devReqs = $parser->getNpmDevRequirements();

    // Then
    $expectedDevReqs = [
        '@eslint/js',
        '@laravel/vite-plugin-wayfinder',
        '@tailwindcss/vite',
        '@types/node',
        '@vitejs/plugin-vue',
        '@vue/eslint-config-typescript',
        'concurrently',
        'eslint',
        'eslint-config-prettier',
        'eslint-plugin-vue',
        'prettier',
        'prettier-plugin-organize-imports',
        'prettier-plugin-tailwindcss',
        'typescript',
        'typescript-eslint',
        'vite',
        'vue-tsc',
    ];

    expect($devReqs)->toBe($expectedDevReqs);
});

test('creating a parser with a project that has no package.json works', function () {
    new Parser('tests/Fixtures/noPackageJson/');
})->throwsNoExceptions();

test('creating a parser with a project that has no composer.json works', function () {
    new Parser('tests/Fixtures/noComposerJson/');
})->throwsNoExceptions();

// Didn't make tests for the other three cases, this may make refactoring the parser more difficult
test("parsing dev requirements of a composer.json file that doesn't have any fails gracefully", function () {
    $parser = new Parser('tests/Fixtures/noComposerDevReqs/');
    $devReqs = $parser->getComposerDevRequirements();
    expect($devReqs)->toBe([]);
});
