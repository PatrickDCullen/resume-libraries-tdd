<?php

use Illuminate\Support\Facades\Storage;

const PROJECT_DIR_NAME = 'resume-libraries-tdd';

// This test depends on the local filesystem rather than something that was set up for testing
test('Projects storage disk has this project in its directories', function () {
    $projectsDirectoryContents = Storage::directories();

    expect(in_array(PROJECT_DIR_NAME, $projectsDirectoryContents))
        ->toBeTrue();
});
