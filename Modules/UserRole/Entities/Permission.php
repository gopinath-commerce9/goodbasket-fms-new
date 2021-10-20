<?php

namespace Modules\UserRole\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    const PERMITTED_YES = 1;
    const PERMITTED_NO = 0;

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
     * Fetches the User Role Map Data of the Permission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roleMap() {
        return $this->hasMany(PermissionMap::class, 'permission_id', 'id');
    }

    /**
     * Fetches the mapped User Role Data
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mappedRoles() {
        return $this->belongsToMany(
            UserRole::class,
            (new PermissionMap())->getTable(),
            'permission_id',
            'role_id'
        )->withPivot('permitted', 'is_active')->withTimestamps();
    }

}
