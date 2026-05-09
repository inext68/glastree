<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contatto extends Model
{
    protected $table = 'contatti';
    protected $fillable = ['individuo_id', 'tipo', 'valore', 'etichetta', 'is_primary'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function individuo(): BelongsTo
    {
        return $this->belongsTo(Individuo::class);
    }
}