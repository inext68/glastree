<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'tenant_id', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = ['name', 'email', 'password', 'tenant_id', 'is_admin'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function notifiche(): HasMany
    {
        return $this->hasMany(Notifica::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotifiche(): HasMany
    {
        return $this->hasMany(Notifica::class)->where('is_read', false)->orderBy('created_at', 'desc');
    }

    public function unreadNotificheCount(): int
    {
        return $this->notifiche()->unread()->count();
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public static function firstUserIsAdmin(): bool
    {
        return self::count() === 1;
    }
}