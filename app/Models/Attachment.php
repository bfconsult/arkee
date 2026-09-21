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

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->url) {
            return null;
        }

        $disk = Storage::disk(config('filesystems.default'));

        // S3 buckets aren't necessarily public-readable, so use a signed URL
        // rather than assuming a public ACL/bucket policy is in place.
        return config('filesystems.default') === 's3'
            ? $disk->temporaryUrl($this->url, now()->addHour())
            : $disk->url($this->url);
    }
}
