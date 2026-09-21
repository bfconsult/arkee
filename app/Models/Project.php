<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'pm_user_id',
        'project_descriptor',
        'site_name',
        'site_address',
        'site_contact_name',
        'next_po_sequence',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function pmUser()
    {
        return $this->belongsTo(User::class, 'pm_user_id');
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
