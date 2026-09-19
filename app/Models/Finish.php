<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finish extends Model
{
    use HasFactory;

    const TYPE_TIMBER_STAIN = 'timber_stain';
    const TYPE_FABRIC = 'fabric';
    const TYPE_OTHER = 'other';

    protected $fillable = [
        'name',
        'type',
    ];

    public function catalogueItems()
    {
        return $this->hasMany(CatalogueItem::class);
    }

    public function scheduleLines()
    {
        return $this->hasMany(FurnitureScheduleLine::class);
    }
}
