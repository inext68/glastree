<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $table = 'tenants';
    protected $fillable = ['nome', 'slug', 'email', 'telefono', 'note', 'attivo'];

    public function individui(): HasMany
    {
        return $this->hasMany(Individuo::class);
    }

    public function gruppi(): HasMany
    {
        return $this->hasMany(Gruppo::class);
    }

    public function documenti(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    public function mailingLists(): HasMany
    {
        return $this->hasMany(MailingList::class);
    }

    public function eventi(): HasMany
    {
        return $this->hasMany(Evento::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}