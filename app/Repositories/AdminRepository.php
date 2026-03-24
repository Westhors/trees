<?php

namespace App\Repositories;

use App\Interfaces\AdminRepositoryInterface;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class AdminRepository extends CrudRepository implements AdminRepositoryInterface
{
    protected Model $model;

    public function __construct(Admin $model)
    {
        $this->model = $model;
    }
}

