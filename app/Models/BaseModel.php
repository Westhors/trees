<?php

namespace App\Models;

use App\Helpers\Constants;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 *
 *
 * @property-read Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @method static Builder|BaseModel filter($filters = null, $filterOperator = '=')
 * @method static Builder|BaseModel newModelQuery()
 * @method static Builder|BaseModel newQuery()
 * @method static Builder|BaseModel onlyTrashed()
 * @method static Builder|BaseModel query()
 * @method static Builder|BaseModel withTrashed()
 * @method static Builder|BaseModel withoutTrashed()
 * @mixin Eloquent
 */
class BaseModel extends Model
{
    use SoftDeletes , LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['*'])->logOnlyDirty();
    }
    public function scopeFilter($builder, $filters = null, $filterOperator = "=")
    {
        if (isset($filters) && is_array($filters)) {
            foreach ($filters as $field => $value) {
                if ($value == Constants::NULL)
                    $builder->whereNull($field);
                elseif ($value == Constants::NOT_NULL)
                    $builder->whereNotNull($field);
                elseif (is_array($value))
                    $builder->whereIn($field, $value);
                elseif ($filterOperator == "like")
                    $builder->where($field, $filterOperator, '%' . $value . '%');
                else
                    $builder->where($field, $value);
            }
        }
        return $builder;
    }
}
