<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\UserRole\Entities\UserRole;
use Modules\UserRole\Entities\UserRoleMap;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Fetches the Role Map Data of the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function roleMap() {
        return $this->hasOne(UserRoleMap::class, 'user_id', 'id');
    }

    /**
     * Fetches the mapped User Role data.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function  mappedRole() {
        return $this->belongsToMany(
            UserRole::class,
            (new UserRoleMap())->getTable(),
            'user_id',
            'role_id'
        )->withPivot('is_active')->withTimestamps();
    }

    /**
     * Checks whether the User is the Default User of the Website.
     * @return bool
     */
    public function isDefaultUser() {
        $defaultAdmin = config('userroles.default.admin_user');
        return (is_array($defaultAdmin) && array_key_exists('email', $defaultAdmin) && ($this->email === $defaultAdmin['email']))
            ? true
            : false;
    }
}
