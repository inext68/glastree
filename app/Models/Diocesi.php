<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diocesi extends Model
{
    protected $table = 'diocesi';
    protected $fillable = ['nome', 'regione'];

    public function gruppi(): HasMany
    {
        return $this->hasMany(Gruppo::class);
    }
}