<?php

namespace App\Services;

class ProjectsService
{
    public static function getParentDirectory()
    {
        return dirname(getcwd());
    }
}
