<?php

use Symfony\Component\Console\Exception\CommandNotFoundException;

test('there is a get-libraries command', function () {
    expect(fn() => $this->artisan('app:get-libraries'))
        ->not()->toThrow(CommandNotFoundException::class);
});
