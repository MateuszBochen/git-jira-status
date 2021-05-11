<?php


namespace App\Service\GitStrategy;


interface GitStrategyInterface
{
    public function getBranches():array;
}