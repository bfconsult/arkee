<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentFinish extends Model
{
    use HasFactory;

    protected $fillable = [
        'furniture_schedule_line_id',
        'component_id',
        'material_id',
        'finish_id',
    ];

    public function furnitureScheduleLine()
    {
        return $this->belongsTo(FurnitureScheduleLine::class);
    }

    public function component()
    {
        return $this->belongsTo(Component::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function finish()
    {
        return $this->belongsTo(Finish::class);
    }
}
