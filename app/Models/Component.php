<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'material_id',
        'name',
        'quantity',
        'notes',
        'supplier_id',
        'code_supplier',
        'unit_cost',
        'meterage',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
