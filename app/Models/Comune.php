<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comune extends Model
{
    protected $table = 'comuni';
    protected $fillable = ['nome', 'codice_istat', 'cap', 'sigla_provincia', 'regione', 'latitudine', 'longitudine'];

    public static function search(string $query)
    {
        return static::where('nome', 'like', "%{$query}%")
            ->orderBy('nome')
            ->limit(20)
            ->get();
    }
}