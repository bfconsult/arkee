<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FurnitureScheduleLine extends Model
{
    use HasFactory;

    const ROW_TYPE_PARENT = 'parent';
    const ROW_TYPE_SUB = 'sub';

    protected $fillable = [
        'project_id',
        'item_id',
        'row_type',
        'parent_line_id',
        'fabric_supplier_id',
        'fabric_notes',
        'quantity',
        'fabric_price_pm',
        'price_override',
        'markup_target_pct',
        'required_by',
        'delivery_location_id',
        'include_on_po',
        'finish_id',
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

    public function parentLine()
    {
        return $this->belongsTo(FurnitureScheduleLine::class, 'parent_line_id');
    }

    public function subLines()
    {
        return $this->hasMany(FurnitureScheduleLine::class, 'parent_line_id');
    }

    public function fabricSupplier()
    {
        return $this->belongsTo(Supplier::class, 'fabric_supplier_id');
    }

    public function deliveryLocation()
    {
        return $this->belongsTo(DeliveryLocation::class);
    }

    public function finish()
    {
        return $this->belongsTo(Finish::class);
    }

    public function purchaseOrders()
    {
        return $this->belongsToMany(PurchaseOrder::class, 'po_schedule_lines', 'schedule_line_id', 'po_id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'entity', 'entity_type', 'entity_id');
    }
}
