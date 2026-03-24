<?php

namespace App\Repositories;

use App\Interfaces\BranchRepositoryInterface;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;

class BranchRepository extends CrudRepository implements BranchRepositoryInterface
{
    protected Model $model;

    public function __construct(Branch $model)
    {
        $this->model = $model;
    }
}

