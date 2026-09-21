<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_PM = 'pm';
    const ROLE_ADMIN = 'admin';
    const ROLE_READ_ONLY = 'read_only';

    const ROLES = [self::ROLE_PM, self::ROLE_ADMIN, self::ROLE_READ_ONLY];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'deleted',
        'timezone',
        'avatar',
        'role',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Not the raw storage path - lets the frontend always just render
     * avatar_url (null when no avatar is set) without knowing about disks.
     */
    protected $appends = ['avatar_url'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deleted' => 'boolean',
            'active' => 'boolean',
        ];
    }

    /**
     * Avatars share the same public storage bucket as Item images (see
     * Attachment::getFileUrlAttribute) - not signed, since the bucket is
     * public-read by design and R2 manages visibility at the bucket level,
     * not per-object.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        return Storage::disk(config('filesystems.default'))->url($this->avatar);
    }

    /**
     * The timezone to render this user's times in server-side (exports,
     * emails) - captured automatically from their browser via
     * CaptureUserTimezone. Falls back to the app's UTC default for a user
     * who hasn't loaded an authenticated page since that middleware shipped.
     */
    public function displayTimezone(): string
    {
        return $this->timezone ?? config('app.timezone');
    }

    /**
     * Projects this user is the PM on.
     */
    public function projectsAsPm()
    {
        return $this->hasMany(Project::class, 'pm_user_id');
    }

    /**
     * Purchase orders assigned to this user.
     */
    public function assignedPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'assignee_user_id');
    }
}
