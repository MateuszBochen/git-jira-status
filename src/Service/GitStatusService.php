<?php


namespace App\Service;

use App\Service\GitStrategy\GitStrategyInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GitStatusService
{
    private GitStrategyInterface $gitStrategy;

    private string $jiraProjectName = '';

    public function __construct(GitStrategyInterface $gitStrategy, ParameterBagInterface $params)
    {
        $jiraParams = $params->get('jira');
        $this->jiraProjectName = $jiraParams['projectName'];

        //var_dump($gitStrategy);
        // $this->gitStrategy = $gitStrategy;

        $branches = $gitStrategy->getBranches();
        echo '<pre>';
        $this->askJiraAboutCommit($branches);
        //print_r($branches);
    }


    public function getStatus()
    {


    }

    private function askJiraAboutCommit(array &$branches)
    {
        foreach ($branches as $branch) {
            $arrayTasks = [];
            $arrayDefects = [];
            $arrayJiraTask = [];
            $arrayUserStories = [];

            if (!isset($branch['commits'])) {
                continue;
            }

            foreach ($branch['commits'] as $commit) {
                preg_match_all('/.*?[tT][aA]([0-9]+)/', $commit['name'], $arrayTasks);
                preg_match_all('/.*?[dD][eE]([0-9]+)/', $commit['name'], $arrayDefects);
                preg_match_all('/.*?' . $this->jiraProjectName . '-([0-9]+)/', $commit['name'], $arrayJiraTask);
                preg_match_all('/.*?[uU][sS]([0-9]+)/', $commit['name'], $arrayUserStories);
            }

            print_r([
                'tasks' => $arrayTasks,
                'de' => $arrayDefects,
                'jira' =>$arrayJiraTask,
                'us' =>$arrayUserStories
            ]);
        }
    }

}
