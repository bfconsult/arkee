<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'contact_email',
        'contact_phone',
        'notes',
        'please_note',
    ];

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Furniture schedule lines where this supplier provides the fabric
     * (distinct from the item's own supplier).
     */
    public function fabricScheduleLines()
    {
        return $this->hasMany(FurnitureScheduleLine::class, 'fabric_supplier_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
