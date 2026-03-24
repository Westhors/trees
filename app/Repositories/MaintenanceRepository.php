<?php

namespace App\Repositories;

use App\Interfaces\MaintenanceRepositoryInterface;
use App\Models\Maintenance;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRepository extends CrudRepository implements MaintenanceRepositoryInterface
{
    protected Model $model;

    public function __construct(Maintenance $model)
    {
        $this->model = $model;
    }
}

