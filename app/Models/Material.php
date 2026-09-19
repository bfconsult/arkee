<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'component_id',
        'name',
        'supplier_id',
        'code_supplier',
        'unit_cost',
        'meterage',
        'notes',
    ];

    public function component()
    {
        return $this->belongsTo(Component::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function colours()
    {
        return $this->hasMany(Colour::class);
    }
}
