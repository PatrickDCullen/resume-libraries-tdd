<?php

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

test('running display-libraries in an empty directory tells the user to move it', function () {
    // Given an empty projects directory

    // When we run the command

    // Then the command returns a suitable output
});
