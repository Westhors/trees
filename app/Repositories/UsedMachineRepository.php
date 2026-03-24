<?php

namespace App\Repositories;

use App\Interfaces\UsedMachineRepositoryInterface;
use App\Models\UsedMachine;
use Illuminate\Database\Eloquent\Model;

class UsedMachineRepository extends CrudRepository implements UsedMachineRepositoryInterface
{
    protected Model $model;

    public function __construct(UsedMachine $model)
    {
        $this->model = $model;
    }
}

