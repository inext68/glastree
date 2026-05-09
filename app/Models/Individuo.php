<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Individuo extends Model
{
    protected $table = 'individui';
    protected $fillable = [
        'tenant_id', 'codice_id', 'cognome', 'nome', 'data_nascita',
        'indirizzo', 'cap', 'città', 'sigla_provincia', 'genere',
        'tipo_documento', 'numero_documento', 'scadenza_documento', 'note', 'avatar_path'
    ];

    protected $casts = [
        'data_nascita' => 'date',
        'scadenza_documento' => 'date',
    ];

    public function hasDocumentoScaduto(): bool
    {
        return $this->scadenza_documento && $this->scadenza_documento->isPast();
    }

    public function hasDocumentoScadeEntroGiorni(int $giorni): bool
    {
        if (!$this->scadenza_documento || $this->scadenza_documento->isPast()) {
            return false;
        }
        return $this->scadenza_documento->diffInDays(null, true) <= $giorni;
    }

    public function getGiorniScadenzaDocumentoAttribute(): ?int
    {
        if (!$this->scadenza_documento) {
            return null;
        }
        return (int) round($this->scadenza_documento->diffInDays(null, true));
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($individuo) {
            if (empty($individuo->codice_id)) {
                $individuo->codice_id = static::generaCodiceId();
            }
        });
    }

    public static function generaCodiceId(): string
    {
        do {
            $codice = str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (static::where('codice_id', $codice)->exists());

        return $codice;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function contatti(): HasMany
    {
        return $this->hasMany(Contatto::class)->orderBy('is_primary', 'desc');
    }

    public function gruppi(): BelongsToMany
    {
        return $this->belongsToMany(Gruppo::class, 'gruppo_individuo')
            ->withPivot('ruolo_nel_gruppo', 'data_adesione')
            ->withTimestamps();
    }

    public function mailingContacts(): HasMany
    {
        return $this->hasMany(MailingContact::class);
    }

    public function eventiResponsabili(): BelongsToMany
    {
        return $this->belongsToMany(Evento::class, 'eventi_responsabili');
    }

    public function avatar(): HasOne
    {
        return $this->hasOne(Documento::class, 'visibilita_target_id')
            ->where('tipologia', 'avatar')
            ->where('visibilita_target_type', self::class);
    }

    public function getNomeCompletoAttribute(): string
    {
        return $this->cognome . ' ' . $this->nome;
    }

    public function getEmailPrimariaAttribute(): ?string
    {
        return $this->contatti()->where('tipo', 'email')->where('is_primary', true)->first()?->valore
            ?? $this->contatti()->where('tipo', 'email')->first()?->valore;
    }

    public function getTelefonoPrimarioAttribute(): ?string
    {
        return $this->contatti()->whereIn('tipo', ['telefono', 'cellulare'])->where('is_primary', true)->first()?->valore
            ?? $this->contatti()->whereIn('tipo', ['telefono', 'cellulare'])->first()?->valore;
    }

    public function documenti(): HasMany
    {
        return $this->hasMany(Documento::class, 'visibilita_target_id')
            ->where('visibilita_target_type', self::class);
    }
}