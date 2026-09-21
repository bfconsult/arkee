<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    const STATUS_QUOTE = 'quote';

    const STATUS_COMPLETE = 'complete';

    const STATUS_APPROVED = 'approved';

    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'project_id',
        'quote_number',
        'version',
        'date',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function furnitureScheduleLines()
    {
        return $this->hasMany(FurnitureScheduleLine::class);
    }
}
