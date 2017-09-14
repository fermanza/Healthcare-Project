<?php

namespace App\Repositories;

use Rinvex\Repository\Repositories\EloquentRepository;

class AccountRepository extends EloquentRepository
{
    protected $repositoryId = 'rinvex.repository.account';
    
    protected $model = 'App\Account';
}