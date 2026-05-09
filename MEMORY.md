# Glastree - MEMORY.md

## Panoramica Progetto

**Glastree** è un sistema web per la gestione di associazioni/organizzazioni basato su Laravel 13 con PHP 8.4, AdminLTE e MySQL.

---

## Stack Tecnologico

- **PHP**: 8.4
- **Laravel**: 13.x
- **Frontend**: AdminLTE 3.2, Bootstrap 4.6, jQuery 3.7.1
- **Database**: MySQL (production), SQLite (dev)
- **Auth**: Custom (Breeze-like)
- **Packages**: spatie/laravel-permission ^7.4

---

## Struttura Database (20 migrazioni)

### Tabelle principali:
- `tenants` - Multi-tenant
- `comuni` - Anagrafe comuni italiani (ISTAT) - seeded
- `diocesi` - Diocesi italiane (384) - seeded
- `users` - Utenti con ruoli Spatie
- `individui` - Persone (codice_id 5 cifre univoco)
- `contatti` - Contatti multiplo per individuo
- `gruppi` - Struttura gerarchica (parent_id self-referencing)
- `gruppo_individuo` - Pivot con ruolo_nel_gruppo, data_adesione
- `eventi` - Eventi con ricorrenza avanzata
- `eventi_gruppi`, `eventi_responsabili`, `eventi_documenti` - Pivot
- `documenti` - Archivio con visibility (pubblico/individuo/gruppo/evento)
- `notifiche` - Sistema notifiche
- `mailing_lists`, `mailing_contacts`, `mailing_messaggi` - Mailing system
- `cache`, `jobs` - Laravel standard

### Campi Eventi (migrazioni 000016/000017/000018):
- `tipo_recorrenza` - enum: singolo, settimanale, mensile, annuale, altro
- `giorno_settimana` - Per ricorrenza settimanale/altro (0-6)
- `giorno_mese` - Riservato (non più usato nella UI)
- `occorrenza_mese` - Per ricorrenza mensile (es. "2,3" = 2° Mercoledì)
- `mesi_recorrenza` - Per ricorrenza mensile multipla (es. "1,3,9")
- `mese_annuale` - Per ricorrenza annuale (1-12)
- `is_incontro_gruppo` - Flag per giorno di incontro gruppo

### Campi Documenti (migrazione 000020):
- `visibilita` - enum: pubblico, gruppo, individuo, associazione, federazione, evento

---

## Modelli Eloquent (15)

| Modello | Tabella | Note |
|---------|---------|------|
| Tenant | tenants | Multi-tenant base |
| User | users | HasRoles (Spatie), is_admin |
| Individuo | individui | Helper scadenza documento |
| Contatto | contatti | Cascade on delete |
| Gruppo | gruppi | Hierarchical, getFullPath |
| Diocesi | diocesi | Seeded 384 diocesi |
| Comune | comuni | Seeded ISTAT |
| Evento | eventi | Ricorrenza avanzata, documenti |
| Documento | documenti | Polymorphic visibility |
| Notifica | notifiche | Scope unread() |
| MailingList | mailing_lists | Opt-in/out |
| MailingContact | mailing_contacts | - |
| MailingMessaggio | mailing_messaggi | Tracking invii |

---

## Controller

| Controller | Metodi | Note |
|------------|--------|------|
| AuthController | login, register, logout | Auto-admin primo utente |
| HomeController | index | Dashboard stats |
| IndividuoController | CRUD completo | NON usa model binding implicito |
| GruppoController | CRUD completo | NON usa model binding implicito |
| EventoController | CRUD completo | Gestione eventi con ricorrenza |
| EventoDocumentoController | store, destroy | Gestione documenti evento |
| ContattoController | update, destroy | PUT/DELETE /contatti/{contatto} |
| DocumentoController | index, store, edit, update, download, preview, destroy, massDestroy, massUpdate, massAssociate | CRUD completo documenti |
| GruppoIndividuoController | store, update, destroy | Gestione appartenenza gruppi (da scheda individuo) |
| GruppoMembroController | store, update, destroy | Gestione membri gruppo (da scheda gruppo) |
| MailingController | nuovo, invio | Gestione invio email singoli/liste |
| MailingListController | CRUD completo | Gestione mailing lists |

### IMPORTANTE - Route Parameter Binding
Le resource routes usano `individui`/`gruppo` come nome parametro.
I controller DEVONO usare `findOrFail($id)` esplicito, NON model type-hint:

```php
// ERRORE - model binding implicito fallisce
public function show(Individuo $individuo) { }

// CORRETTO
public function show($individuo) {
    $individuo = Individuo::with(['contatti', 'gruppi'])->findOrFail($individuo);
}
```

### Route Parameter Naming
- `individui` (non `individuo`) per le route resource
- `gruppo` (non `gruppi`) per le route resource
- Usare URL helper `url('/individui/' . $id)` invece di `route()` per evitare UrlGenerationException

---

## Routes web.php

```
/                        → redirect (auth → dashboard / guest → login)
/login, /register        → AuthController (guest middleware)
/logout                 → POST (auth)
/dashboard              → HomeController (auth)

Resource:
/individui              → CRUD (auth) - param: individui
/gruppi                 → CRUD (auth) - param: gruppo
/eventi                 → CRUD (auth) - param: evento

// Gruppo Membri (before resource routes)
POST /gruppi/{gruppo}/membri                 → GruppoMembroController@store
PUT/PATCH /gruppi/{gruppo}/membri/{individuo} → GruppoMembroController@update
DELETE /gruppi/{gruppo}/membri/{individuo}   → GruppoMembroController@destroy

// Evento Documenti (before resource routes)
POST /eventi/{evento}/documenti                         → EventoDocumentoController@store
DELETE /eventi/{eventi}/documenti/{documento}          → EventoDocumentoController@destroy

// Individuo Gruppi
POST/PUT/PATCH/DELETE /individui/{individuo}/gruppi/{gruppo} → GruppoIndividuoController

// Contatti
PUT/PATCH/DELETE /contatti/{contatto}       → ContattoController

// Documenti
POST /documenti                            → DocumentoController@store
GET /documenti/{documento}/download        → DocumentoController@download
GET /documenti/{documento}/preview         → DocumentoController@preview (modal iframe)
DELETE /documenti/{documento}              → DocumentoController@destroy
```

---

## Views

### Layout:
- `layouts/adminlte.blade.php` - AdminLTE con sidebar, navbar, notifications

### Individui:
- `individui/index.blade.php` - Tabella con paginazione 20
- `individui/show.blade.php` - Scheda completa con:
  - 3 colonne: Anagrafica, Residenza, Documento di identità
  - Badge scadenza documento (rosso=scaduto, giallo=≤30gg)
  - Tabella contatti con edit inline e azioni
  - Tabella gruppi associati (add/edit/remove inline)
  - Tabella documenti (upload/preview/delete)
- `individui/create.blade.php` - Form con contatti dinamici JS
- `individui/edit.blade.php` - Form precompilato con:
  - Contatti dinamici JS (add/remove)
  - Gruppi (add/edit/remove inline)
  - Documenti (upload/preview/delete con preview modal)

### Gruppi:
- `gruppi/index.blade.php` - Albero gerarchico flat
- `gruppi/show.blade.php` - Scheda con:
  - Info gruppo, luogo incontro, statistiche
  - **Card "Giorno di Incontro"** (se evento is_incontro_gruppo)
  - **Tabella documenti** (visualizzazione + download)
  - Membri con azioni (visualizza)
  - Sottogruppi con azioni (visualizza/modifica/elimina)
- `gruppi/create.blade.php` - Form con membri dinamici JS
- `gruppi/edit.blade.php` - Form precompilato con:
  - Responsabile selezionabile SOLO tra i membri del gruppo (include opzione "Nessuno")
  - Membri in modalità multiriga con azioni (modifica inline/elimina per riga)
  - Documenti (upload/preview/delete con preview modal)

### Eventi:
- `eventi/index.blade.php` - Elenco con filtri per gruppo e tipo ricorrenza
- `eventi/show.blade.php` - Dettaglio con gruppi, responsabili e documenti
- `eventi/create.blade.php` - Form con:
  - Tipo ricorrenza: singolo/settimanale/mensile/annuale/altro
  - Campi dinamici: occorrenza mese / mesi multipli / mese singolo
  - is_incontro_gruppo checkbox
- `eventi/edit.blade.php` - Form precompilato con documenti

### Auth:
- `auth/login.blade.php`
- `auth/register.blade.php`

### Documenti:
- `documenti/index.blade.php` - Elenco multiriga con selezione multipla e azioni di massa
- `documenti/edit.blade.php` - Modifica singolo documento

### Home:
- `home/dashboard.blade.php` - Stats cards + ultime notifiche

---

## Eventi - Ricorrenza Avanzata

### Tipo Ricorrenza e Campi Correlati

| Tipo | Campi |
|------|-------|
| `singolo` | data_specifica |
| `settimanale` | giorno_settimana (0-6) |
| `mensile` | occorrenza_mese (es. "2,3" = 2° Mercoledì), mesi_recorrenza (multipli) |
| `annuale` | mese_annuale (1-12) |
| `altro` | giorno_settimana |

### Occorrenza Mese (per ricorrenza mensile)
Formato stringa: "occorrenza,giorno_settimana"
- 1,1 = 1° Lunedì, 1,2 = 1° Martedì, ..., 1,0 = 1° Domenica
- 2,1 = 2° Lunedì, ..., 4,6 = 4° Sabato

### Helper Evento Model
```php
$evento->isRicorrente()           // bool
$evento->isSingolo()              // bool
$evento->isEventoRicorrenteSettimanale() // bool
$evento->isEventoRicorrenteMensile()     // bool
$evento->isEventoRicorrenteAnnuale()    // bool
$evento->giorno_settimana_label   // "Lunedì", "Martedì", etc.
$evento->periodicita_label       // "Settimanale", "Mensile", etc.
$evento->occorrenza_mensile_label // "2° Mercoledì"
$evento->mesi_recorrenza_label   // "Gennaio, Marzo, Settembre"
$evento->mese_annuale_label      // "Luglio"
$evento->info_ricorrenza         // Stringa descrittiva
```

### Giorno di Incontro Gruppo
- Campo `is_incontro_gruppo` (boolean)
- Se true, mostra card "Giorno di Incontro" nella show del gruppo
- Display condizionale basato su tipo ricorrenza

---

## Helpers / Accessors Individuo

```php
// Scadenza documento (diffInDays con absolute=true per valori positivi)
$individuo->hasDocumentoScaduto()              // bool
$individuo->hasDocumentoScadeEntroGiorni(30)   // bool (≤30gg)
$individuo->giorni_scadenza_documento           // int|null (valore assoluto)

// Contatti
$individuo->nome_completo     // "Cogn Nom"
$individuo->email_primaria    // string|null
$individuo->telefono_primario // string|null

// Gruppi
$gruppo->full_path     // "Child > Parent > Root"
$gruppo->getAncestors()    // array
$gruppo->getDescendants()  // array
```

---

## Contatti - Funzionalità Inline

### URL Detection
I tipi `web` e `telegram` mostrano link clickabile solo se il valore inizia con protocollo conosciuto:
`http://`, `https://`, `ftp://`, `ssh://`, `sftp://`, `telnet://`

### Edit Inline nella Show
- Pulsante edit per ogni riga contatto
- Toggle: riga → form inline nella stessa riga
- Campi: tipo (select), valore, etichetta, primario
- Salva → PUT `/contatti/{id}` → redirect con success
- Annulla → ripristina riga originale

---

## Membri Gruppo - Funzionalità Inline

### Gestione Membri nel Edit Gruppo
- Pulsante aggiungi apre form inline per associare nuovo membro
- Tabella membri con: individuo (link), codice, contatti, ruolo, data adesione
- Modifica inline: toggle riga → form inline con campi ruolo/data
- Rimuovi: DELETE /gruppi/{gruppo}/membri/{individuo}

### Responsabile Gruppo
- Selezionabile SOLO tra i membri del gruppo
- Può essere lasciato vuoto ("Nessuno")

### Route GruppoMembroController
- `POST /gruppi/{gruppo}/membri` → store (aggiunge membro)
- `PUT/PATCH /gruppi/{gruppo}/membri/{individuo}` → update (modifica ruolo/data)
- `DELETE /gruppi/{gruppo}/membri/{individuo}` → destroy (rimuove membro)

---

## Documenti - Preview Modal

### Preview in Modal
- Pulsante anteprima apre modal Bootstrap con iframe
- Supporta immagini e PDF
- URL: `/documenti/{id}/preview` → iframe src
- URL: `/documenti/{id}/download` → pulsante scarica

### Tipologie documento
- Individui: avatar, galleria, documento, statuto, altro
- Gruppi: documento, statuto, avatar, galleria, altro
- Eventi: documento, programma, locandina, altro

### Visibilità
- pubblico, gruppo, individuo, associazione, federazione, evento

---

## Convenzioni

- **Tabelle**: snake_case plurale
- **Modelli**: PascalCase singolare
- **Controller**: PascalCase + Controller
- **Routes**: kebab-case
- **Codice**: PSR-12, PHP 8 attributes, no docblock comments
- **No model binding implicito** nei controller (usare findOrFail)
- **Carbon diffInDays**: usare `diffInDays(null, true)` per valore assoluto
- **URL helper**: usare `url()` invece di `route()` per sottodirectory
- **Route ordering**: routes specifici PRIMA delle resource routes
- **PHP 8.4**: concatenazione stringhe SEMPRE con `.` (non `+`)

---

## Errori Comuni da Evitare

### Concatenazione stringhe in PHP 8.4
In PHP 8.4 la concatenazione richiede il punto (`.`), non il segno `+`:
```php
// ERRORE - TypeError: Unsupported operand types: string + string
$label = $individuo->cognome + ' ' + $individuo->nome;

// CORRETTO
$label = $individuo->cognome . ' ' . $individuo->nome;
```

### Arrow functions in Blade
Quando si usano arrow functions in Blade con `@json()`, assicurarsi che la sintassi sia corretta:
```php
// CORRETTO
const individui = @json($individui->map(fn($i) => ['id' => $i->id, 'label' => $i->cognome . ' ' . $i->nome]));
```

### Form annidati in HTML
In HTML non sono consentiti form annidati (form dentro form). Se si usa un form principale per il salvataggio dati e si aggiungono form per azioni inline (es. aggiungi membro, associa gruppo), questi ultimi devono essere fuori dal form principale.

**Soluzione**: Usare card/container separati con input fields (non form) e inviare i dati via JavaScript/AJAX con `fetch()`.

### Doppia section @section('scripts')
In Blade, se si definisce `@section('scripts')` due volte nello stesso file, solo la prima viene usata. Questo causa la scomparsa delle funzioni JavaScript definite nella seconda.

**Esempio ERRORE**:
```blade
@section('scripts')
@endsection

<!-- contenuto pagina -->

@section('scripts')
<script>
function miaFunzione() { }
</script>
@endsection
```

**Soluzione**: Eliminare la prima section vuota e mantenere solo quella con il codice.

---

## Todo / Lavori da completare

- [ ] CRUD Mailing + views
- [ ] Funzionalità Notifiche (mark as read, link)
- [ ] Middleware tenant_id per scoping automatico
- [ ] Policy/Gate per autorizzazioni
- [ ] Form Request per validazione
- [ ] Resource/ResourceCollection per API
- [ ] Tests PHPUnit
- [x] Modifica contatti inline nella show
- [x] URL detection per protocolli conosciuti
- [x] Gestione appartenenza gruppi da scheda individuo (add/edit/remove)
- [x] Upload/documenti e download da scheda individuo
- [x] Preview documento in modal (iframe Bootstrap)
- [x] Label "Documento di identità" invece di "Documento"
- [x] Gestione membri gruppo in modalità multiriga con azioni inline (edit/delete)
- [x] GruppoMembroController per CRUD membri gruppo
- [x] CRUD Eventi completo (index, create, edit, show)
- [x] Ricorrenza avanzata: settimanale/mensile/annuale/altro
- [x] Campo is_incontro_gruppo e card "Giorno di Incontro" nel gruppo
- [x] Responsabile gruppo selezionabile solo tra i membri
- [x] Documenti per eventi (upload/preview/delete)
- [x] Lista documenti in show gruppo (visualizzazione + download)
- [x] Validazione responsabile "Nessuno" in create/edit gruppo
- [x] Pagina elenco documenti con azioni di massa (elenco, associa, cambia tipo/visibilità)
- [x] Sistema viste report (salva/configura colonne, filtri, ordinamento, ricerca, export)
- [x] Menu "Utilità Viste" per gestione viste salvate
- [x] Toolbar inline individui (ricerca/filtri, esporta, salva vista)
- [x] Azioni per riga: Cancella (modal granulare), Stampa, Esporta CSV, Invia Email, Genera Report
- [x] Route /individui/{id}/collegati - JSON con conteggi elementi collegati
- [x] Controller metodo elementiCollegati($id) - restituisce JSON con contatti, gruppi, documenti, eventi
- [x] Controller metodo elimina(Request, $id) - logica granulare eliminazione con opzioni
- [x] Modal eliminazione con checkboxes (contatti, gruppi, documenti, eventi)
- [x] 5 pulsanti azioni per riga: Cancella (modal granulare), Stampa, Esporta CSV, Invia Email, Genera Report
- [x] Importazione CSV individui con view, controller e rotte
- [x] Download template CSV con tutti i campi supportati
- [ ] Report stampabile con righe espanse per entità collegate

---

## Modifiche Recenti

### 2026-05-08 - Viste Report
- Nuova tabella `viste_report` per salvare configurazioni tabella (migrazione 000022)
- Nuovo modello `VistaReport` con relazione user
- Nuovo `VistaReportController` per CRUD viste
- View `viste/index.blade.php` per elenco viste salvate
- Menu "Utilità Viste" nel sidebar
- Implementato in `individui/index.blade.php` con toolbar inline
- Toolbar con: Ricerca/Filtri, Esporta (CSV), Salva Vista
- Pannello ricerca espandibile con filtri per colonna
- Ordinamento click su header
- Toggle visibilità colonne

### 2026-05-09 - Report Stampabile con Righe Espanse (IN CORSO)

#### Implementato
- Route `GET /individui/{id}/collegati` - ritorna JSON con conteggi
- Controller metodo `elementiCollegati($id)` - conta contatti, gruppi, documenti, eventi
- Controller metodo `elimina(Request, $id)` con logica granulare:
  - Contatti: se checked → DELETE, altrimenti → setta individuo_id = null
  - Gruppi: solo detach
  - Documenti: se checked → elimina file + record, altrimenti → scollega
  - Eventi: solo detach
- Modal eliminazione in `individui/index.blade.php` con checkboxes
- 5 pulsanti azioni per riga: Cancella, Stampa, Esporta, Invia, Report
- JavaScript: showDeleteModal, confirmDelete, printIndividuo, exportIndividuoCSV, sendEmail, generateReport

#### Todo
- Implementare stampa con righe espanse (anteprima PDF scheda individuo)
- Implementare generazione report PDF completo

### 2026-05-08 - Gestione Documenti
- Nuova pagina `/documenti` con elenco multiriga (index)
- Colonne: checkbox selezione, Nome, Nome file, Tipologia, Contesto, Riferimento, Data, Utente, Azioni
- Azioni di massa: Elimina, Associa a (individuo/gruppo/evento), Cambia tipo/visibilità
- Pagina edit per modificare singolo documento
- Campi user_id nella tabella documenti per tracciare chi inserisce
- Voce menu laterale "Documenti" collegata alla lista
- **BUGFIX**: Corretto errore concatenazione stringhe in PHP 8.4 (`+` → `.`)

### 2026-05-08 - Bugfix Gruppi
- Aggiunta sezione documenti nella view show.blade.php con tabella documenti
- Caricamento documenti nel controller show() con eager loading 'documenti'
- Corretta validazione responsabile_id per accettare opzione "Nessuno" (stringa vuota)

### 2026-05-08 - Eventi
- Nuovo EventoController con CRUD completo
- Views: index, create, edit, show
- **Ricorrenza avanzata**:
  - Settimanale: giorno della settimana (0-6)
  - Mensile: occorrenza_mese (1°-4° + giorno settimana) + mesi multipli selezionabili
  - Annuale: mese singolo selezionabile
  - Altro: giorno della settimana
- **Campo is_incontro_gruppo** per identificare giorno di incontro
- **Card "Giorno di Incontro"** nella show del gruppo con display condizionale
- **Documenti evento**: upload/preview/delete con preview modal
- Migrazione 000016: giorno_mese, mesi_recorrenza, mese_annuale
- Migrazione 000017: occorrenza_mese (sostituisce giorno_mese nella UI)
- Migrazione 000018: aggiornamento enum tipo_recorrenza
- Migrazione 000019: tabella eventi_documenti

### 2026-05-08 - Gruppi
- Show: riorganizzato layout con statistiche
- Show: card "Giorno di Incontro" per eventi con is_incontro_gruppo=true
- Show: tabella membri con codice/email/telefono/ruolo/data
- Show: tabella sottogruppi con diocesi/responsabile/conteggio + delete
- Create: URL mappa e responsabile popolato JS
- Create: lista membri con codice/contatti/ruolo/data adesione
- Edit: responsabile selezionabile SOLO tra i membri del gruppo
- Edit: gestione membri in modalità multiriga con azioni inline (modifica/elimina)
- Edit: documenti section (upload/preview/delete con preview modal)
- Nuovo GruppoMembroController per gestione membri (store/update/destroy)

### 2026-05-08 - Individui
- Label "Documento di identità" aggiornato in create/edit/show

### 2026-05-08 - Database
- Fresh migrate con seed (384 diocesi, comuni ISTAT)
- Migrazione 000020: aggiunto "evento" a enum visibilita documenti

---

## Regole di Debug

- **Verificare sempre codice duplicato** - Quando una funzionalità non funziona (es. click su pulsante non fa nulla), controllare se c'è codice duplicato/corrotto nel file JavaScript o Blade che può rompere la struttura
- Prima di modificare un file, verificare sempre che non ci siano blocchi di codice ripetuti o mal posizionati

---

## File Chiave

- `AGENTS.md` - Istruzioni per agenti
- `MEMORY.md` - Database contesto (questo file)
- `.env` - Configurazione ambiente
- `composer.json` - Dipendenze
- `database/seeders/DiocesiSeeder.php` - 384 diocesi italiane
- `database/seeders/ComuniSeeder.php` - Cap ISTAT
- `app/Http/Controllers/GruppoMembroController.php` - CRUD membri gruppo
- `app/Http/Controllers/EventoController.php` - CRUD eventi
- `app/Http/Controllers/EventoDocumentoController.php` - Documenti eventi

---

## Correzioni Recenti

### 2025-05-09
- **Route parameter fix**: mailing-liste/edit usava oggetto invece di ID nei link
- **sendEmail fix**: Parametro JS `individuo` -> `individui` per coerenza con controller
- **mailing/nuovo**: Rimosso form nascosto lista mailing quando si invia a individui singoli
- **IndividuoController**: Rimosso doppio closing brace che causava parse error
- **index.blade.php**: mailing-liste.show usa $lista->id invece di $lista oggetto
- **Mailing List creation**: 
  - Modal in individui: semplificato, solo nome + descrizione, precompila contatti da selezione
  - Mailing-liste/create: aggiunto multiselect gruppi e individui, salvataggio contatti
- **Route ordering**: specifiche routes spostate PRIMA delle resource routes per evitare 404
- **cogname field**: Database ha colonna `cogname` (singola 'n'), controller usa `$arr['cogname']` da toArray()
- **MailingListController**: show() usa findOrFail() esplicito invece di model binding implicito