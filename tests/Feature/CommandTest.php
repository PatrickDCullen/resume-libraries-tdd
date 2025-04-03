<?php

use Tests\TestCase;
use Symfony\Component\Console\Exception\CommandNotFoundException;

test('there is a get-libraries command', function () {
    expect(fn() => $this->artisan('app:get-libraries'))
        ->not()->toThrow(CommandNotFoundException::class);
});

test('the get-libraries command outputs something', function() {
    $this->artisan('app:get-libraries')
        ->expectsOutput()
        ->assertSuccessful();
});

test('artisan does not output the default command description', function() {
    $this->artisan('list')
        ->doesntExpectOutputToContain('Command description')
        ->assertSuccessful();
});
