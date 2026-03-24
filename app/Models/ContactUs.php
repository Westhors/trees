<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;

/**
 *
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $subject
 * @property string|null $message
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @method static Builder|ContactUs filter($filters = null, $filterOperator = '=')
 * @method static Builder|ContactUs newModelQuery()
 * @method static Builder|ContactUs newQuery()
 * @method static Builder|ContactUs onlyTrashed()
 * @method static Builder|ContactUs query()
 * @method static Builder|ContactUs whereAddress($value)
 * @method static Builder|ContactUs whereCreatedAt($value)
 * @method static Builder|ContactUs whereDeletedAt($value)
 * @method static Builder|ContactUs whereEmail($value)
 * @method static Builder|ContactUs whereId($value)
 * @method static Builder|ContactUs whereMessage($value)
 * @method static Builder|ContactUs whereName($value)
 * @method static Builder|ContactUs wherePhone($value)
 * @method static Builder|ContactUs whereSubject($value)
 * @method static Builder|ContactUs whereUpdatedAt($value)
 * @method static Builder|ContactUs withTrashed()
 * @method static Builder|ContactUs withoutTrashed()
 * @mixin Eloquent
 */
class ContactUs extends BaseModel
{
    protected $guarded = ['id'];

}
