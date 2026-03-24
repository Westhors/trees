<?php

namespace App\Repositories;

use App\Interfaces\EventRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;

class EventRepository extends CrudRepository implements EventRepositoryInterface
{
    protected Model $model;

    public function __construct(Event $model)
    {
        $this->model = $model;
    }
}
