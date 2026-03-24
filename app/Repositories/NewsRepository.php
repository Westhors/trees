<?php

namespace App\Repositories;

use App\Interfaces\NewsRepositoryInterface;
use App\Models\News;
use Illuminate\Database\Eloquent\Model;

class NewsRepository extends CrudRepository implements NewsRepositoryInterface
{
    protected Model $model;

    public function __construct(News $model)
    {
        $this->model = $model;
    }
}

