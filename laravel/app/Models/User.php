<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'institute_id',
        'name',
        'email',
        'username',
        'password',
        'role',
        'phone',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'is_active'         => 'boolean',
            'password'          => 'hashed',
        ];
    }

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function batchAssignments()
    {
        return $this->hasMany(UserBatchAssignment::class);
    }

    public function subjectAssignments()
    {
        return $this->hasMany(UserSubjectAssignment::class);
    }

    public function subAdminPermissions()
    {
        return $this->hasOne(SubAdminPermission::class);
    }

    public function isRole(string ...$roles): bool
    {
        return in_array($this->role, $roles);
    }
}
