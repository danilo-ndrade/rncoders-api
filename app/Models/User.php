<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPassword;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'password',
        'email',
    ];

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token, $this->name));
    }

    /**
     * Roles of the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Permissions of the user
     *
     * @return array<string>
     */
    public function permissions()
    {
        $permissions = Permission::all();

        $userPermissions = [];

        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo($permission->slug)) {
                $userPermissions[] = $permission->slug;
            }
        }

        return $userPermissions;
    }

    /**
     * Verifies if user has any roles.
     *
     * @param string|mixed $roles
     *
     * @return bool
     */
    public function hasAnyRoles($roles)
    {
        if (is_string($roles)) {
            return $this->roles->contains('slug', $roles);
        }

        return !!$roles->intersect($this->roles)->count();
    }

    /**
     * Verifies if user has permission
     *
     * @param string $permissionSlug
     *
     * @return bool
     */
    public function hasPermissionTo($permissionSlug)
    {
        if ($this->hasAnyRoles('developer')) {
            return true;
        }

        $permission = Permission::where('slug', $permissionSlug)->first();

        return !is_null($permission) ? $this->hasAnyRoles($permission->roles) : false;
    }
}
