<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueItem extends Model
{
    use HasFactory;

    protected $table = 'items_catalog';

    const ROW_TYPE_PARENT = 'parent';
    const ROW_TYPE_SUB = 'sub';

    protected $fillable = [
        'catalogue_no',
        'row_type',
        'item_type',
        'supplier_id',
        'code_supplier',
        'notes_supplier',
        'unit_cost',
        'meterage',
        'parent_item_id',
        'height_mm',
        'width_mm',
        'depth_mm',
        'packaging_type',
        'finish_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function parentItem()
    {
        return $this->belongsTo(CatalogueItem::class, 'parent_item_id');
    }

    public function subItems()
    {
        return $this->hasMany(CatalogueItem::class, 'parent_item_id');
    }

    public function finish()
    {
        return $this->belongsTo(Finish::class);
    }

    public function scheduleLines()
    {
        return $this->hasMany(FurnitureScheduleLine::class, 'item_id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'entity', 'entity_type', 'entity_id');
    }
}
