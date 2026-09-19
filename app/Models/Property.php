<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'name',
        'address',
        'email',
    ];

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'roles')->withPivot('type')->withTimestamps();
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }
}
