<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Documento extends Model
{
    protected $table = 'documenti';
    protected $fillable = [
        'tenant_id', 'user_id', 'nome_file', 'file_path', 'tipo', 'tipologia',
        'visibilita', 'visibilita_target_id', 'visibilita_target_type',
        'mime_type', 'dimensione', 'note'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function target(): MorphTo
    {
        return $this->morphTo('visibilita_target_type', 'visibilita_target_id');
    }

    public function eventi(): BelongsToMany
    {
        return $this->belongsToMany(Evento::class, 'eventi_documenti')->withTimestamps();
    }

    public function gruppi(): BelongsToMany
    {
        return $this->belongsToMany(Gruppo::class, 'gruppi_documenti')->withTimestamps();
    }

    public function individui(): BelongsToMany
    {
        return $this->belongsToMany(Individuo::class, 'individui_documenti')->withTimestamps();
    }

    public function isVisibilePerIndividuo(Individuo $individuo): bool
    {
        if ($this->visibilita === 'pubblico') return true;
        
        if ($this->visibilita === 'individuo' && $this->visibilita_target_id === $individuo->id) return true;
        
        if ($this->visibilita === 'gruppo' && $this->visibilita_target_type === Gruppo::class) {
            return $individuo->gruppi()->where('gruppi.id', $this->visibilita_target_id)->exists();
        }
        
        return false;
    }
}