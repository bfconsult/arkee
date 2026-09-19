<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_RECEIVED = 'received';

    protected $fillable = [
        'po_number',
        'project_id',
        'supplier_id',
        'delivery_location_id',
        'assignee_user_id',
        'order_status',
        'date_issued',
    ];

    protected $casts = [
        'date_issued' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function deliveryLocation()
    {
        return $this->belongsTo(DeliveryLocation::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_user_id');
    }

    public function scheduleLines()
    {
        return $this->belongsToMany(FurnitureScheduleLine::class, 'po_schedule_lines', 'po_id', 'schedule_line_id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'entity', 'entity_type', 'entity_id');
    }
}
