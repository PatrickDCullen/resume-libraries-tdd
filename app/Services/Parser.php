<?php

namespace App\Services;

class Parser
{
    /**
     * Root level directory where composer.json
     * and package.json can be found
     */
    protected string $projectRoot;

    protected string $composerJsonContents;

    protected string $packageJsonContents;

    public function __construct(string $projectRoot)
    {
        $this->composerJsonContents = file_get_contents($projectRoot.'composer.json');
        $this->packageJsonContents = file_get_contents($projectRoot.'package.json');
    }

    public function getComposerRequirements(): array
    {
        return array_keys(json_decode($this->composerJsonContents, true)['require']);
    }

    public function getComposerDevRequirements()
    {
        return array_keys(json_decode($this->composerJsonContents, true)['require-dev']);
    }

    public function getNpmRequirements()
    {
        return array_keys(json_decode($this->packageJsonContents, true)['dependencies']);
    }
}
