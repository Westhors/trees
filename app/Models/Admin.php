<?php

namespace App\Models;

use Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Activitylog\Models\Activity;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property bool $super_admin
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property Carbon|null $deleted_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|Admin filter($filters = null, $filterOperator = '=')
 * @method static Builder|Admin newModelQuery()
 * @method static Builder|Admin newQuery()
 * @method static Builder|Admin onlyTrashed()
 * @method static Builder|Admin query()
 * @method static Builder|Admin whereCreatedAt($value)
 * @method static Builder|Admin whereDeletedAt($value)
 * @method static Builder|Admin whereEmail($value)
 * @method static Builder|Admin whereEmailVerifiedAt($value)
 * @method static Builder|Admin whereId($value)
 * @method static Builder|Admin whereName($value)
 * @method static Builder|Admin wherePassword($value)
 * @method static Builder|Admin whereRememberToken($value)
 * @method static Builder|Admin whereSuperAdmin($value)
 * @method static Builder|Admin whereUpdatedAt($value)
 * @method static Builder|Admin withTrashed()
 * @method static Builder|Admin withoutTrashed()
 * @mixin Eloquent
 */
class Admin extends BaseModel
{
    use HasApiTokens, HasFactory, Notifiable ,Authenticatable;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'super_admin' => 'boolean',
    ];

}
