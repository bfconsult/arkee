<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'catalogue_no',
        'item_category_id',
        'height_mm',
        'width_mm',
        'depth_mm',
        'packaging_type_id',
        'supplier_id',
        'notes',
    ];

    public function itemCategory()
    {
        return $this->belongsTo(ItemCategory::class);
    }

    public function packagingType()
    {
        return $this->belongsTo(PackagingType::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function components()
    {
        return $this->hasMany(Component::class);
    }

    public function scheduleLines()
    {
        return $this->hasMany(FurnitureScheduleLine::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'entity', 'entity_type', 'entity_id');
    }
}
