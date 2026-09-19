<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    const KIND_IMAGE = 'image';
    const KIND_FABRIC_STAIN = 'fabric_stain';
    const KIND_PO_PDF = 'po_pdf';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'kind',
        'url',
    ];

    /**
     * Column names (entity_type/entity_id) match the doc rather than
     * Laravel's default attachable_* morph convention.
     */
    public function entity()
    {
        return $this->morphTo('entity', 'entity_type', 'entity_id');
    }
}
