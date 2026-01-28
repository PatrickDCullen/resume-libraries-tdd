<?php

namespace App\Services;

class Parser
{
    /**
     * Root level directory where composer.json
     * and package.json can be found
     */
    protected string $projectRoot;

    protected string $fileContents;

    public function __construct(string $projectRoot)
    {
        $this->fileContents = file_get_contents($projectRoot.'composer.json');
    }

    public function getComposerRequirements(): array
    {
        return array_keys(json_decode($this->fileContents, true)['require']);
    }
}
