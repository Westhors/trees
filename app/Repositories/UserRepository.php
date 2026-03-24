<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends CrudRepository implements UserRepositoryInterface
{
    protected Model $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
