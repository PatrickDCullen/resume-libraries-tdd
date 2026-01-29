<?php

namespace App\Services;

class Parser
{
    /**
     * Root level directory where composer.json
     * and package.json can be found
     */
    protected string $projectRoot;

    protected ?string $composerJsonContents = null;

    protected ?string $packageJsonContents = null;

    public function __construct(string $projectRoot)
    {
        if (file_exists($projectRoot.'composer.json')) {
            $this->composerJsonContents = file_get_contents($projectRoot.'composer.json');
        }

        if (file_exists($projectRoot.'package.json')) {
            $this->packageJsonContents = file_get_contents($projectRoot.'package.json');
        }
    }

    public function getComposerRequirements(): array
    {
        if (! $this->composerJsonContents) {
            return [];
        }

        if (! array_key_exists('require', json_decode($this->composerJsonContents, true))) {
            return [];
        }

        return array_keys(json_decode($this->composerJsonContents, true)['require']);
    }

    public function getComposerDevRequirements(): array
    {
        if (! $this->composerJsonContents) {
            return [];
        }

        if (! array_key_exists('require-dev', json_decode($this->composerJsonContents, true))) {
            return [];
        }

        return array_keys(json_decode($this->composerJsonContents, true)['require-dev']);
    }

    public function getNpmRequirements(): array
    {
        if (! $this->packageJsonContents) {
            return [];
        }

        if (! array_key_exists('dependencies', json_decode($this->packageJsonContents, true))) {
            return [];
        }

        return array_keys(json_decode($this->packageJsonContents, true)['dependencies']);
    }

    public function getNpmDevRequirements(): array
    {
        if (! $this->packageJsonContents) {
            return [];
        }

        if (! array_key_exists('devDependencies', json_decode($this->packageJsonContents, true))) {
            return [];
        }

        return array_keys(json_decode($this->packageJsonContents, true)['devDependencies']);
    }
}
