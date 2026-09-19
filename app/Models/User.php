<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
        'current_property_id',
        'timezone',
        'claimed_at',
        'avatar',
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
            'claimed_at' => 'datetime',
            'password' => 'hashed',
            'deleted' => 'boolean',
        ];
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        $disk = Storage::disk(config('filesystems.default'));

        // S3 buckets aren't necessarily public-readable, so use a signed URL
        // rather than assuming a public ACL/bucket policy is in place.
        return config('filesystems.default') === 's3'
            ? $disk->temporaryUrl($this->avatar, now()->addHour())
            : $disk->url($this->avatar);
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'roles')->withPivot('type')->withTimestamps();
    }

    /**
     * Properties this user has ever created - approximated as "properties
     * they currently hold the admin role on", since PropertyController::store()
     * makes the creator an admin immediately and there's no dedicated
     * creator column on properties. Not exact if admin access is later
     * granted to/revoked from someone else.
     */
    public function adminProperties()
    {
        return $this->belongsToMany(Property::class, 'roles')->wherePivot('type', Role::ADMIN);
    }

    /**
     * The timezone to render this user's times in server-side (PDF/Excel
     * exports, emails) - captured automatically from their browser via
     * CaptureUserTimezone. Falls back to the app's UTC default for a user
     * who hasn't loaded an authenticated page since that middleware shipped.
     */
    public function displayTimezone(): string
    {
        return $this->timezone ?? config('app.timezone');
    }

    /**
     * Whether this user has ever set their own password and logged in -
     * false for a "shell" record a manager/admin added directly (just to
     * log time against) that hasn't been invited/claimed yet.
     */
    public function isClaimed(): bool
    {
        return $this->claimed_at !== null;
    }

    /**
     * Users it's actually possible to email - excludes shell records with
     * no email on file and anyone who was added but never claimed their
     * account, for anything that emails users in bulk (digests, reminders).
     */
    public function scopeContactable($query)
    {
        return $query->whereNotNull('email')->whereNotNull('claimed_at');
    }

    public function roleOn(Property $property): ?string
    {
        return $this->roles()
            ->where('property_id', $property->id)
            ->value('type');
    }

    public function isAdminOn(Property $property): bool
    {
        return $this->roleOn($property) === Role::ADMIN;
    }

    public function isManagerOn(Property $property): bool
    {
        return in_array($this->roleOn($property), [Role::ADMIN, Role::MANAGER]);
    }

    public function isWorkerOn(Property $property): bool
    {
        return in_array($this->roleOn($property), [Role::ADMIN, Role::MANAGER, Role::WORKER]);
    }
}
