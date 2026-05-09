<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailingMessaggio extends Model
{
    protected $table = 'mailing_messaggi';
    protected $fillable = [
        'tenant_id', 'mailing_list_id', 'user_id', 'oggetto', 'corpo',
        'canale', 'stato', 'inviato_al', 'totale_destinatari', 'invii_ok', 'invii_falliti'
    ];

    protected $casts = [
        'inviato_al' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function mailingList(): BelongsTo
    {
        return $this->belongsTo(MailingList::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isInviato(): bool
    {
        return $this->stato === 'inviato';
    }

    public function canBeSent(): bool
    {
        return in_array($this->stato, ['bozza', 'fallito']);
    }
}