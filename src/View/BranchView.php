<?php

namespace App\View;

class BranchView
{
    private string $name;
    private string $url;
    private array $commits;

    /**
     * BranchView constructor.
     * @param string $name
     * @param string $url
     * @param array $commits
     * @author Mateusz Bochen
     */
    public function __construct(string $name, string $url, array $commits)
    {
        $this->name = $name;
        $this->url = $url;
        $this->commits = $commits;
    }

    /**
     * @return string
     * @author Mateusz Bochen
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     * @author Mateusz Bochen
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return array
     * @author Mateusz Bochen
     */
    public function getCommits(): array
    {
        return $this->commits;
    }
}
