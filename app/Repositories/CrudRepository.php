<?php

namespace App\Repositories;

use App\Helpers\Constants;
use App\Interfaces\Interfaces\ICrudRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CrudRepository implements ICrudRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all($with = [], $conditions = [], $columns = array('*'))
    {
        $order_by = request(Constants::ORDER_BY) ?? "id";
        $deleted = request(Constants::Deleted) ?? false;
        $order_by_direction = request(Constants::ORDER_By_DIRECTION) ?? "asc";
        $filter_operator = request(Constants::FILTER_OPERATOR) ?? "=";
        $filters = request(Constants::FILTERS) ?? [];
        $per_page = request(Constants::PER_PAGE) ?? 15;
        $paginate = request(Constants::PAGINATE) ?? true;
        $query = $this->model;
        if ($deleted == true) {
            $query = $query->onlyTrashed();
        }

        $all_conditions = array_merge($conditions, $filters);
        foreach ($filters as $key => $value) {
            if (is_numeric($value)) {
                $query = $query->where($key, '=', $value);
            } else {
                $query = $query->where($key, 'LIKE', '%' . $value . '%');
            }
        }
        if (isset($order_by) && !empty($with))
            $query = $query->with($with)->orderBy($order_by, $order_by_direction);
        if ($paginate && !empty($with))
            return $query->with($with)->paginate($per_page, $columns);
        if (isset($order_by))
            $query = $query->orderBy($order_by, $order_by_direction);
        if ($paginate)
            return $query->paginate($per_page, $columns);
        if (!empty($with))
            return $query->with($with)->get($columns);
        else
            return $query->get($columns);
    }

    // Eager load database relationships
    public function AddMediaCollection($name = 'media', $model, $collection = 'default')
    {
        $oldMedia = $model->media()->where('collection', $collection)->first();
        if ($oldMedia) {
            $model->media()->detach($oldMedia->id);
        }
        $model->media()->attach([isset(request()->get($name)['id']) ? request()->get($name)['id'] : request()->get($name) => ['collection' => $collection]]);
    }


    // Eager load database relationships
    public function AddMediaCollectionArray($name = 'media', $model, $collection = 'default')
    {
        $oldMedia = $model->media()->where('collection', $collection)->get();
        if ($oldMedia->count() > 0) {
            foreach ($oldMedia as $key => $value) {
                $model->media()->detach($value->id);
            }
        }
        foreach (request()->get($name) as $key => $value) {
            $model->media()->attach([isset($value['id']) ? $value['id'] : $value => ['collection' => $collection]]);
        }
    }

    public function update(array $data, $id, $attribute = "id")
    {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    public function create(array $data)
    {
        $filteredData = collect($data)->toArray();
        return $this->model->create($filteredData);
    }

    // Set the associated model
    public function destroy($model)
    {
        $model->delete();
        return $model;
    }

    public function deleteRecords($tableName, $ids, $relationsToNeglect = [])
    {
        $destroyDenied = [];

        if (Schema::hasColumn($tableName, 'deleted_at')) {
            DB::table($tableName)
                ->whereIn('id', $ids)
                ->update(['deleted_at' => Carbon::now()]);
        } else {
            DB::table($tableName)->whereIn('id', $ids)->delete();
        }

        return count($destroyDenied);
    }






    public function deleteRecordsFinial($modelClass, $ids, $relationsToNeglect = [])
    {
        $destroyDenied = [];
        $modelClass::whereIn('id', $ids)->forceDelete();
        return count($destroyDenied);
    }




    public function restoreItem($modelName, $ids, $relationsToNeglect = [])
    {
        $destroyDenied = [];
        $modelName::whereIn('id', $ids)->restore();
        return count($destroyDenied);
    }



    public function restore($model)
    {
        $model->restore();
        return $model;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }


    public function findInAll($id)
    {
        return  $this->model->withTrashed()->findOrFail($id);
    }

    // remove record from the database

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    // remove record from the database

    public function findTrashed($id)
    {
        return $this->model->onlyTrashed()->findOrFail($id);
    }
}
