<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductRepository extends CrudRepository implements ProductRepositoryInterface
{
    protected Model $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }
}

