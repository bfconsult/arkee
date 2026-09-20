<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FurnitureScheduleLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'item_id',
        'fabric_notes',
        'quantity',
        'price_override',
        'markup_target_pct',
        'required_by',
        'include_on_po',
        'internal_cost_manual',
    ];

    protected $casts = [
        'required_by' => 'date',
        'include_on_po' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function purchaseOrders()
    {
        return $this->belongsToMany(PurchaseOrder::class, 'po_schedule_lines', 'schedule_line_id', 'po_id');
    }

    public function fabricComponents()
    {
        return $this->hasMany(FabricComponent::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'entity', 'entity_type', 'entity_id');
    }
}
