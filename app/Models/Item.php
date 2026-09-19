<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalogue_no',
        'item_type',
        'height_mm',
        'width_mm',
        'depth_mm',
        'packaging_type_id',
        'notes',
    ];

    public function packagingType()
    {
        return $this->belongsTo(PackagingType::class);
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
