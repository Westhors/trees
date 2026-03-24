<?php

namespace App\Interfaces\Interfaces;

interface ICrudRepository
{
    public function all($with = [], $conditions = [], $columns = array('*'));
    public function update(array $data, $id, $attribute = "id");


    public function AddMediaCollection($name, $model, $collection = 'default');

    public function  create(array $data);

    public function AddMediaCollectionArray($name, $model, $collection = 'default');

    public function destroy($model);

    public function findInAll($id);

    public function findTrashed($id);


    public function find($id);

    public function restore($model);

    public function delete($id);

    public function deleteRecordsFinial($tableName, $ids, $relationsToNeglect = []);

    public function deleteRecords($tableName, $ids, $relationsToNeglect = []);

    public function restoreItem($tableName, $ids, $relationsToNeglect = []);

}
