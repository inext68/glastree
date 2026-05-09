<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MailingList extends Model
{
    protected $table = 'mailing_lists';
    protected $fillable = ['tenant_id', 'user_id', 'nome', 'descrizione', 'attiva'];

    protected $casts = ['attiva' => 'boolean'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contatti(): HasMany
    {
        return $this->hasMany(MailingContact::class);
    }

    public function messaggi(): HasMany
    {
        return $this->hasMany(MailingMessaggio::class);
    }

    public function getIndividui()
    {
        return Individuo::whereHas('mailingContacts', function ($q) {
            $q->where('mailing_list_id', $this->id)->where('opt_in', true);
        })->get();
    }
}