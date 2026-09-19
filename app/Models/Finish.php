<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finish extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'name',
        'code_supplier',
        'notes',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
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
