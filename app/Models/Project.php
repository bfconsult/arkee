<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    const STATUS_QUOTE = 'quote';

    const STATUS_COMPLETE = 'complete';

    const STATUS_APPROVED = 'approved';

    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'quote_number',
        'client_id',
        'pm_user_id',
        'project_descriptor',
        'version',
        'date',
        'status',
        'site_name',
        'site_address',
        'site_contact_name',
        'next_po_sequence',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function pmUser()
    {
        return $this->belongsTo(User::class, 'pm_user_id');
    }

    public function furnitureScheduleLines()
    {
        return $this->hasMany(FurnitureScheduleLine::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
