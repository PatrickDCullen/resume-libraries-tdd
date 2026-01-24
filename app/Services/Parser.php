<?php

namespace App\Services;

class Parser
{
    public static function getComposerRequirements(string $fileContents)
    {
        return array_keys(json_decode($fileContents, true)['require']);
    }
}
