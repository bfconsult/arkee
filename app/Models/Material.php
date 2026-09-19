<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'supplier_id',
        'code_supplier',
        'unit_cost',
        'meterage',
        'notes',
    ];

    public function components()
    {
        return $this->hasMany(Component::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function finishes()
    {
        return $this->hasMany(Finish::class);
    }
}
