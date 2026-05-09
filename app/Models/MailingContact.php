<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailingContact extends Model
{
    protected $table = 'mailing_contacts';
    protected $fillable = ['mailing_list_id', 'individuo_id', 'opt_in', 'opt_in_data', 'opt_out_data'];

    protected $casts = [
        'opt_in' => 'boolean',
        'opt_in_data' => 'datetime',
        'opt_out_data' => 'datetime',
    ];

    public function mailingList(): BelongsTo
    {
        return $this->belongsTo(MailingList::class);
    }

    public function individuo(): BelongsTo
    {
        return $this->belongsTo(Individuo::class);
    }
}