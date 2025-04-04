<?php

use App\Services\ProjectsService;

test('ProjectsService getParentDirectory() method returns a string', function () {
    expect(ProjectsService::getParentDirectory())
        ->toBeString();
});

test('ProjectsService getParentDirectory() method returns a directory', function () {
    expect(ProjectsService::getParentDirectory())
        ->toBeReadableDirectory();
});

test('ProjectsService getParentdirectory() method returns a directory that contains this project', function() {
        $projectsDirectory = Storage::build([
            'driver' => 'local',
            'root' => ProjectsService::getParentDirectory(),
        ]);

    expect(in_array("resume-libraries-tdd", $projectsDirectory->directories()))
        ->toBeTrue();
});
