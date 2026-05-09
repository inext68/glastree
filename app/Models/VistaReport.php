<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VistaReport extends Model
{
    protected $table = 'viste_report';
    protected $fillable = [
        'user_id', 'nome', 'tipo', 'colonne_visibili', 
        'colonne_ordinamento', 'filtri', 'ricerca'
    ];

    protected $casts = [
        'colonne_visibili' => 'array',
        'colonne_ordinamento' => 'array',
        'filtri' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}