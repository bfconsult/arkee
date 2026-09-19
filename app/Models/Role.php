<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['user_id', 'project_id', 'type'];

    const TYPES = ['admin', 'manager', 'worker', 'approver'];

    const ADMIN = 'admin';
    const MANAGER = 'manager';
    const WORKER = 'worker';
    const APPROVER = 'approver';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}