<?php


namespace App\Service\GitStrategy;

use Gitlab\Client;
use Gitlab\ResultPager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GitLabStrategy implements GitStrategyInterface
{
    private Client $gitlabClient;

    private string $projectId;
    private string $masterBranchName;
    private string $releaseBranchName;

    public function __construct(ParameterBagInterface $params)
    {
        $gitParams = $params->get('git');
        //var_dump($gitParams);

        $this->gitlabClient = new Client();
        $this->gitlabClient->setUrl($gitParams['url']);
        $this->gitlabClient->authenticate($gitParams['token'], Client::AUTH_HTTP_TOKEN);

        $this->projectId = $gitParams['projectId'];
        $this->masterBranchName = $gitParams['masterBranchName'];
        $this->releaseBranchName = $gitParams['releaseBranchName'];
    }


    public function getBranches():array
    {
        $repositories = $this->gitlabClient->repositories();

        // $branches = $repositories->branches($this->projectId, ['per_page' => 999]);

        $pager = new ResultPager($this->gitlabClient);
        $branches = $pager->fetchAll(
            $repositories,
            'branches',
            [$this->projectId]
        );


        //var_dump($branches);

        if (!$branches || empty($branches)) {
            return [];
        }

        $collection = [];
        foreach ($branches as $gitLabBranchArray) {
            $collection[$gitLabBranchArray['name']] = [
                'name' => $gitLabBranchArray['name'],
                'url' => $gitLabBranchArray['web_url'],
                'commit' => $gitLabBranchArray['commit']['id']
            ];
        }

        $this->getCommits($collection);

        return $collection;
    }


    private function getCommits(array &$branchesArray)
    {
        foreach ($branchesArray as &$branchArray) {
            if ($branchArray['name'] === $this->masterBranchName) {
                continue;
            }

            if ($branchArray['name'] === $this->releaseBranchName) {
                $commits = $this->compare($branchArray['name'], $this->masterBranchName);
            } else {
                $commits = $this->compare($branchArray['name'], $this->releaseBranchName);
            }

            if (isset($commits['commits'])) {
                foreach ($commits['commits'] as $gitLabCommit) {
                    $branchArray['commits'][] = [
                        'author' => $gitLabCommit['author_name'],
                        'name' => $gitLabCommit['title'],
                        'date' => $gitLabCommit['created_at'],
                        'url' => $gitLabCommit['web_url'],
                    ];
                }
            }
        }
    }

    private function compare(string $branchNameFrom, string $branchNameTo)
    {
        return $this->gitlabClient->repositories()
                ->compare($this->projectId, $branchNameTo, $branchNameFrom);
    }
}
