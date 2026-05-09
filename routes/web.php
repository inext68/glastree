<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndividuoController;
use App\Http\Controllers\GruppoController;
use App\Http\Controllers\ContattoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\GruppoIndividuoController;
use App\Http\Controllers\GruppoMembroController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\EventoDocumentoController;
use App\Http\Controllers\VistaReportController;
use App\Http\Controllers\MailingController;
use App\Http\Controllers\MailingListController;

// Landing
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard')->middleware('auth');

// Resource routes
// Individui specific routes (MUST be before resource route)
Route::get('individui/email-list', [IndividuoController::class, 'emailList'])->middleware('auth');
Route::get('individui/all', [IndividuoController::class, 'allIndividui'])->middleware('auth');

Route::get('individui/import', [IndividuoController::class, 'import'])->middleware('auth')->name('individui.import');
Route::post('individui/import', [IndividuoController::class, 'importStore'])->middleware('auth')->name('individui.import.store');
Route::get('individui/template', [IndividuoController::class, 'downloadTemplate'])->middleware('auth')->name('individui.template');
Route::get('individui/{individuo}/report', [IndividuoController::class, 'report'])->middleware('auth')->name('individui.report');
Route::get('individui/{individuo}/collegati', [IndividuoController::class, 'elementiCollegati'])->middleware('auth');
Route::post('individui/{individuo}/elimina', [IndividuoController::class, 'elimina'])->middleware('auth');
Route::resource('individui', IndividuoController::class)->middleware('auth');

// Gruppo Membri routes (before resource to avoid conflict)
Route::post('gruppi/{gruppo}/membri', [GruppoMembroController::class, 'store'])->middleware('auth');
Route::put('gruppi/{gruppo}/membri/{individuo}', [GruppoMembroController::class, 'update'])->middleware('auth');
Route::patch('gruppi/{gruppo}/membri/{individuo}', [GruppoMembroController::class, 'update'])->middleware('auth');
Route::delete('gruppi/{gruppo}/membri/{individuo}', [GruppoMembroController::class, 'destroy'])->middleware('auth');

// Gruppi specific routes (MUST be before resource route)
Route::get('gruppi/all', [GruppoController::class, 'allGruppi'])->middleware('auth');
Route::get('gruppi/{gruppo}/individui-email', [GruppoController::class, 'individuiEmail'])->middleware('auth');

Route::resource('gruppi', GruppoController::class)->middleware('auth');
Route::resource('eventi', EventoController::class)->middleware('auth');
Route::post('eventi/{evento}/documenti', [EventoDocumentoController::class, 'store'])->middleware('auth');
Route::delete('eventi/{evento}/documenti/{documento}', [EventoDocumentoController::class, 'destroy'])->middleware('auth');
Route::delete('contatti/{contatto}', [ContattoController::class, 'destroy'])->name('contatti.destroy')->middleware('auth');
Route::put('contatti/{contatto}', [ContattoController::class, 'update'])->name('contatti.update')->middleware('auth');
Route::patch('contatti/{contatto}', [ContattoController::class, 'update'])->name('contatti.update')->middleware('auth');

Route::post('individui/{individuo}/gruppi', [GruppoIndividuoController::class, 'store'])->middleware('auth');
Route::put('individui/{individuo}/gruppi/{gruppo}', [GruppoIndividuoController::class, 'update'])->middleware('auth');
Route::patch('individui/{individuo}/gruppi/{gruppo}', [GruppoIndividuoController::class, 'update'])->middleware('auth');
Route::delete('individui/{individuo}/gruppi/{gruppo}', [GruppoIndividuoController::class, 'destroy'])->middleware('auth');

Route::get('documenti', [DocumentoController::class, 'index'])->name('documenti.index')->middleware('auth');
Route::post('documenti', [DocumentoController::class, 'store'])->middleware('auth');

Route::post('documenti/mass-destroy', [DocumentoController::class, 'massDestroy'])->middleware('auth');
Route::post('documenti/mass-update', [DocumentoController::class, 'massUpdate'])->middleware('auth');
Route::post('documenti/mass-associate', [DocumentoController::class, 'massAssociate'])->middleware('auth');

Route::get('documenti/{documento}/edit', [DocumentoController::class, 'edit'])->middleware('auth');
Route::put('documenti/{documento}', [DocumentoController::class, 'update'])->middleware('auth');
Route::get('documenti/{documento}/download', [DocumentoController::class, 'download'])->middleware('auth');
Route::get('documenti/{documento}/preview', [DocumentoController::class, 'preview'])->middleware('auth');
Route::delete('documenti/{documento}', [DocumentoController::class, 'destroy'])->middleware('auth');

Route::get('viste', [VistaReportController::class, 'index'])->name('viste.index')->middleware('auth');
Route::post('viste', [VistaReportController::class, 'store'])->middleware('auth');
Route::get('viste/{vistaReport}/json', [VistaReportController::class, 'showJson'])->middleware('auth');
Route::put('viste/{vistaReport}', [VistaReportController::class, 'update'])->middleware('auth');
Route::patch('viste/{vistaReport}/default', [VistaReportController::class, 'setDefault'])->middleware('auth');
Route::delete('viste/{vistaReport}', [VistaReportController::class, 'destroy'])->middleware('auth');

Route::get('mailing/nuovo', [MailingController::class, 'nuovo'])->middleware('auth')->name('mailing.nuovo');
Route::post('mailing/invia', [MailingController::class, 'invia'])->middleware('auth')->name('mailing.invia');
Route::get('mailing/invio', [MailingController::class, 'invio'])->middleware('auth')->name('mailing.invio');
Route::post('mailing/invio-elabora', [MailingController::class, 'invioElabora'])->middleware('auth')->name('mailing.invio.elabora');

Route::resource('mailing-liste', MailingListController::class)->middleware('auth');
Route::post('mailing-liste/create-from-individui', [MailingListController::class, 'createFromIndividui'])->middleware('auth');