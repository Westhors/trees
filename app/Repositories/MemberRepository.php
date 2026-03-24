<?php

namespace App\Repositories;

use App\Interfaces\MemberRepositoryInterface;
use App\Models\Member;
use Illuminate\Database\Eloquent\Model;

class MemberRepository extends CrudRepository implements MemberRepositoryInterface
{
    protected Model $model;

    public function __construct(Member $model)
    {
        $this->model = $model;
    }
}

