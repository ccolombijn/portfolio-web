<?php

namespace App\Repositories;

use App\Contracts\ProjectRepositoryInterface; 

class JsonProjectRepository extends JsonRepository implements ProjectRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('projects');
    }
}