<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    protected $table = 'eventi';

    protected $fillable = [
        'tenant_id',
        'nome_evento',
        'descrizione_evento',
        'tipo_evento',
        'tipo_recorrenza',
        'giorno_settimana',
        'giorno_mese',
        'occorrenza_mese',
        'mesi_recorrenza',
        'mese_annuale',
        'ora_inizio',
        'data_specifica',
        'durata_minuti',
        'descrizione',
        'note',
        'is_incontro_gruppo',
    ];

    protected $casts = [
        'data_specifica' => 'date',
        'ora_inizio' => 'datetime:H:i',
        'is_incontro_gruppo' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function gruppi(): BelongsToMany
    {
        return $this->belongsToMany(Gruppo::class, 'eventi_gruppi')->withTimestamps();
    }

    public function responsabili(): BelongsToMany
    {
        return $this->belongsToMany(Individuo::class, 'eventi_responsabili')->withTimestamps();
    }

    public function documenti(): BelongsToMany
    {
        return $this->belongsToMany(Documento::class, 'eventi_documenti')->withTimestamps();
    }

    public function isRicorrente(): bool
    {
        return $this->tipo_recorrenza !== null && $this->tipo_recorrenza !== 'singolo';
    }

    public function isSingolo(): bool
    {
        return $this->tipo_recorrenza === 'singolo' || $this->tipo_recorrenza === null;
    }

    public function isGlobale(): bool
    {
        return $this->gruppi()->count() === 0;
    }

    public function isEventoRicorrenteSettimanale(): bool
    {
        return $this->tipo_recorrenza === 'settimanale';
    }

    public function isEventoRicorrenteMensile(): bool
    {
        return $this->tipo_recorrenza === 'mensile';
    }

    public function isEventoRicorrenteAnnuale(): bool
    {
        return $this->tipo_recorrenza === 'annuale';
    }

    public function isEventoAltro(): bool
    {
        return $this->tipo_recorrenza === 'altro';
    }

    public function isIncroGruppo(): bool
    {
        return $this->is_incontro_gruppo === true;
    }

    public function getGiornoSettimanaLabelAttribute(): string
    {
        $giorni = [
            0 => 'Domenica',
            1 => 'Lunedì',
            2 => 'Martedì',
            3 => 'Mercoledì',
            4 => 'Giovedì',
            5 => 'Venerdì',
            6 => 'Sabato',
        ];
        return $giorni[$this->giorno_settimana] ?? '-';
    }

    public function getGiornoSettimanaList(): array
    {
        return [
            0 => ['value' => 0, 'label' => 'Domenica'],
            1 => ['value' => 1, 'label' => 'Lunedì'],
            2 => ['value' => 2, 'label' => 'Martedì'],
            3 => ['value' => 3, 'label' => 'Mercoledì'],
            4 => ['value' => 4, 'label' => 'Giovedì'],
            5 => ['value' => 5, 'label' => 'Venerdì'],
            6 => ['value' => 6, 'label' => 'Sabato'],
        ];
    }

    public function getOccorrenzeMensiliList(): array
    {
        $giorni = ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'];
        $list = [];
        for ($occ = 1; $occ <= 4; $occ++) {
            foreach ($giorni as $idx => $giorno) {
                $value = $occ . ',' . $idx;
                $label = $occ . '° ' . $giorno;
                $list[] = ['value' => $value, 'label' => $label];
            }
        }
        return $list;
    }

    public function getOccorrenzaMensileLabelAttribute(): string
    {
        if (!$this->occorrenza_mese) {
            return '-';
        }
        
        $parts = explode(',', $this->occorrenza_mese);
        if (count($parts) !== 2) {
            return '-';
        }
        
        $occorrenza = (int) $parts[0];
        $giorno = (int) $parts[1];
        
        $giorni = ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'];
        
        return $occorrenza . '° ' . ($giorni[$giorno] ?? '-');
    }

    public function getMesiList(): array
    {
        return [
            1 => ['value' => 1, 'label' => 'Gennaio'],
            2 => ['value' => 2, 'label' => 'Febbraio'],
            3 => ['value' => 3, 'label' => 'Marzo'],
            4 => ['value' => 4, 'label' => 'Aprile'],
            5 => ['value' => 5, 'label' => 'Maggio'],
            6 => ['value' => 6, 'label' => 'Giugno'],
            7 => ['value' => 7, 'label' => 'Luglio'],
            8 => ['value' => 8, 'label' => 'Agosto'],
            9 => ['value' => 9, 'label' => 'Settembre'],
            10 => ['value' => 10, 'label' => 'Ottobre'],
            11 => ['value' => 11, 'label' => 'Novembre'],
            12 => ['value' => 12, 'label' => 'Dicembre'],
        ];
    }

    public function getMesiRecorrenzaLabelAttribute(): string
    {
        if (!$this->mesi_recorrenza) {
            return '-';
        }
        
        $mesiSelezionati = explode(',', $this->mesi_recorrenza);
        $mesiLabels = $this->getMesiList();
        
        $labels = array_map(function ($mese) use ($mesiLabels) {
            return $mesiLabels[$mese]['label'] ?? $mese;
        }, $mesiSelezionati);
        
        return implode(', ', $labels);
    }

    public function getMeseAnnualeLabelAttribute(): string
    {
        if (!$this->mese_annuale) {
            return '-';
        }
        
        $mesiLabels = $this->getMesiList();
        return $mesiLabels[$this->mese_annuale]['label'] ?? '-';
    }

    public function getPeriodicitaLabelAttribute(): string
    {
        $labels = [
            'settimanale' => 'Settimanale',
            'mensile' => 'Mensile',
            'annuale' => 'Annuale',
            'altro' => 'Altro',
            'singolo' => 'Singolo',
        ];
        return $labels[$this->tipo_recorrenza] ?? '-';
    }

    public function getInfoRicorrenzaAttribute(): string
    {
        if ($this->tipo_recorrenza === 'settimanale') {
            return 'Ogni ' . $this->giorno_settimana_label;
        }
        
        if ($this->tipo_recorrenza === 'mensile') {
            $info = [];
            if ($this->occorrenza_mese) {
                $info[] = $this->occorrenza_mensile_label;
            }
            if ($this->mesi_recorrenza) {
                $info[] = '(' . $this->mesi_recorrenza_label . ')';
            }
            return implode(' ', $info) ?: '-';
        }
        
        if ($this->tipo_recorrenza === 'annuale') {
            if ($this->mese_annuale) {
                return $this->mese_annuale_label;
            }
        }
        
        if ($this->tipo_recorrenza === 'altro') {
            return $this->giorno_settimana_label ?? '-';
        }
        
        return '-';
    }

    public function getInfoIncroAttribute(): ?string
    {
        if (!$this->is_incontro_gruppo) {
            return null;
        }

        $info = [];
        
        if ($this->isRicorrente()) {
            $info[] = $this->info_ricorrenza;
        } else {
            $info[] = $this->data_specifica?->format('d/m/Y') ?? '-';
        }

        if ($this->ora_inizio) {
            $info[] = 'ore ' . $this->ora_inizio->format('H:i');
        }

        return implode(' ', $info);
    }
}