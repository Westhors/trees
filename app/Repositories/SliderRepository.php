<?php

namespace App\Repositories;

use App\Interfaces\SliderRepositoryInterface;
use App\Models\Slider;
use Illuminate\Database\Eloquent\Model;

class SliderRepository extends CrudRepository implements SliderRepositoryInterface
{
    protected Model $model;

    public function __construct(Slider $model)
    {
        $this->model = $model;
    }
}

