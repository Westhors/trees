<?php

namespace App\Repositories;

use App\Interfaces\AdminRepositoryInterface;
use App\Interfaces\ContactUsRepositoryInterface;
use App\Models\Admin;
use App\Models\ContactUs;
use Illuminate\Database\Eloquent\Model;

class ContactUsRepository extends CrudRepository implements ContactUsRepositoryInterface
{
    protected Model $model;

    public function __construct(ContactUs $model)
    {
        $this->model = $model;
    }
}
