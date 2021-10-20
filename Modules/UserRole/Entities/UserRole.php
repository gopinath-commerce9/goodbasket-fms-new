<?php

namespace Modules\UserRole\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserRole extends Model
{
    use HasFactory;

    const ADMIN_ROLE = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'code',
        'display_name',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Fetches the User Map Data of the Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userMap() {
        return $this->hasMany(UserRoleMap::class, 'role_id', 'id');
    }

    /**
     * Fetches the Permissions Map Data of the Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissionMap() {
        return $this->hasMany(PermissionMap::class, 'role_id', 'id');
    }

    /**
     * Fetches the mapped Users Data
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mappedUsers() {
        return $this->belongsToMany(
            User::class,
            (new UserRoleMap())->getTable(),
            'role_id',
            'user_id'
        )->withPivot('is_active')->withTimestamps();
    }

    /**
     * Fetches the mapped Permissions Data
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mappedPermissions() {
        return $this->belongsToMany(
            Permission::class,
            (new PermissionMap())->getTable(),
            'role_id',
            'permission_id'
        )->withPivot('is_active')->withTimestamps();
    }

    /**
     * Checks whether the Role is Admin.
     * @return bool
     */
    public function isAdmin() {
        return ($this->code === self::ADMIN_ROLE)
            ? true
            : false;
    }

}
