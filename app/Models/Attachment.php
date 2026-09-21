<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
     * Despite the column name, `url` holds the storage disk path, not a
     * servable URL - matches User::avatar. The frontend should always read
     * file_url instead.
     */
    protected $appends = ['file_url'];

    /**
     * Column names (entity_type/entity_id) match the doc rather than
     * Laravel's default attachable_* morph convention.
     */
    public function entity()
    {
        return $this->morphTo('entity', 'entity_type', 'entity_id');
    }

    /**
     * Unlike User::avatar_url, this is never signed - the bucket behind the
     * s3 disk is public-read by design, since attachment links need to stay
     * valid indefinitely (emailed quotes, exported PDFs, long-idle tabs),
     * not just for the ~1hr a signed URL would last.
     */
    public function getFileUrlAttribute(): ?string
    {
        if (! $this->url) {
            return null;
        }

        return Storage::disk(config('filesystems.default'))->url($this->url);
    }
}
