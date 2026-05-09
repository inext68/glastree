<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Gruppo extends Model
{
    protected $table = 'gruppi';
    protected $fillable = [
        'tenant_id', 'parent_id', 'diocesi_id', 'responsabile_id',
        'nome', 'descrizione', 'indirizzo_incontro', 'cap_incontro',
        'città_incontro', 'sigla_provincia_incontro', 'mappa_posizione', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Gruppo::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Gruppo::class, 'parent_id');
    }

    public function diocesi(): BelongsTo
    {
        return $this->belongsTo(Diocesi::class);
    }

    public function responsabile(): BelongsTo
    {
        return $this->belongsTo(Individuo::class, 'responsabile_id');
    }

    public function individui(): BelongsToMany
    {
        return $this->belongsToMany(Individuo::class, 'gruppo_individuo')
            ->withPivot('ruolo_nel_gruppo', 'data_adesione')
            ->withTimestamps();
    }

    public function eventi(): BelongsToMany
    {
        return $this->belongsToMany(Evento::class, 'eventi_gruppi')->withTimestamps();
    }

    public function documenti(): HasMany
    {
        return $this->hasMany(Documento::class, 'visibilita_target_id')
            ->where('visibilita_target_type', self::class);
    }

    public function getAncestors(): array
    {
        $ancestors = [];
        $current = $this->parent;
        while ($current) {
            $ancestors[] = $current;
            $current = $current->parent;
        }
        return $ancestors;
    }

    public function getDescendants(): array
    {
        $descendants = [];
        foreach ($this->children as $child) {
            $descendants[] = $child;
            $descendants = array_merge($descendants, $child->getDescendants());
        }
        return $descendants;
    }

    public function getFullPathAttribute(): string
    {
        $path = [$this->nome];
        foreach (array_reverse($this->getAncestors()) as $ancestor) {
            $path[] = $ancestor->nome;
        }
        return implode(' > ', array_reverse($path));
    }
}