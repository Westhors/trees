<?php

namespace App\Models;


use App\Traits\HasMedia;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string|null $address_line_one
 * @property string|null $address_line_two
 * @property string $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property string $website
 * @property string|null $phone
 * @property int $members_count
 * @property int $business_est
 * @property string|null $profile
 * @property string $fpp
 * @property string $title
 * @property string $first_name
 * @property string $last_name
 * @property string $job_title
 * @property string $phone_number
 * @property string $cell_number
 * @property string $email
 * @property Carbon|null $birth_date
 * @property Carbon|null $email_verified_at
 * @property mixed|null $password
 * @property string|null $unhashed_password
 * @property string|null $remember_token
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $country_id
 * @property-read Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @property-read Country|null $country
 * @property-read Collection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereAddressLineOne($value)
 * @method static Builder|User whereAddressLineTwo($value)
 * @method static Builder|User whereBirthDate($value)
 * @method static Builder|User whereBusinessEst($value)
 * @method static Builder|User whereCellNumber($value)
 * @method static Builder|User whereCity($value)
 * @method static Builder|User whereCountryId($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereFpp($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereJobTitle($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User whereMembersCount($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhone($value)
 * @method static Builder|User wherePhoneNumber($value)
 * @method static Builder|User wherePostalCode($value)
 * @method static Builder|User whereProfile($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereState($value)
 * @method static Builder|User whereTitle($value)
 * @method static Builder|User whereUnhashedPassword($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereWebsite($value)
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, LogsActivity , HasMedia ,SoftDeletes;

    protected $with = [
        'media',
    ];

    protected $casts = [
        'active' => 'boolean',
        'show_home' => 'boolean',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['*']);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'members_count' => 'integer',
            'business_est' => 'integer',
        ];
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'company_id');
    }

}

