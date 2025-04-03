<?php

use Tests\TestCase;
use Symfony\Component\Console\Exception\CommandNotFoundException;

test('there is a display-libraries command', function () {
    expect(fn() => $this->artisan('app:display-libraries'))
        ->not()->toThrow(CommandNotFoundException::class);
});

test('the get-libraries command outputs something', function() {
    $this->artisan('app:display-libraries')
        ->expectsOutput()
        ->assertSuccessful();
});

test('artisan does not output the default command description', function() {
    $this->artisan('list')
        ->doesntExpectOutputToContain('Command description')
        ->assertSuccessful();
});
