<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected mixed $crudRepository;

    public function toggle($id, $column)
    {
        $item = $this->crudRepository->find($id);

        if ($item) {
            $item->$column = !$item->$column;
            $item->save();

            return response()->json(['message' => 'Status Changed successfully']);
        }

        return response()->json(['Error' => 'Item does not exist'], 404);
    }
}
