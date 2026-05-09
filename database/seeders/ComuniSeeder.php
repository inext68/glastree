<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comune;

class ComuniSeeder extends Seeder
{
    public function run(): void
    {
        $comuni = [
            ['nome' => 'Roma', 'codice_istat' => '058091', 'cap' => '00100', 'sigla_provincia' => 'RM', 'regione' => 'Lazio'],
            ['nome' => 'Milano', 'codice_istat' => '015146', 'cap' => '20100', 'sigla_provincia' => 'MI', 'regione' => 'Lombardia'],
            ['nome' => 'Napoli', 'codice_istat' => '063138', 'cap' => '80100', 'sigla_provincia' => 'NA', 'regione' => 'Campania'],
            ['nome' => 'Torino', 'codice_istat' => '001272', 'cap' => '10100', 'sigla_provincia' => 'TO', 'regione' => 'Piemonte'],
            ['nome' => 'Palermo', 'codice_istat' => 'G273', 'cap' => '90100', 'sigla_provincia' => 'PA', 'regione' => 'Sicilia'],
            ['nome' => 'Genova', 'codice_istat' => '009969', 'cap' => '16100', 'sigla_provincia' => 'GE', 'regione' => 'Liguria'],
            ['nome' => 'Bologna', 'codice_istat' => '037015', 'cap' => '40100', 'sigla_provincia' => 'BO', 'regione' => 'Emilia-Romagna'],
            ['nome' => 'Firenze', 'codice_istat' => '048017', 'cap' => '50100', 'sigla_provincia' => 'FI', 'regione' => 'Toscana'],
            ['nome' => 'Bari', 'codice_istat' => 'A662', 'cap' => '70100', 'sigla_provincia' => 'BA', 'regione' => 'Puglia'],
            ['nome' => 'Catania', 'codice_istat' => 'C351', 'cap' => '95100', 'sigla_provincia' => 'CT', 'regione' => 'Sicilia'],
            ['nome' => 'Venezia', 'codice_istat' => 'L736', 'cap' => '30100', 'sigla_provincia' => 'VE', 'regione' => 'Veneto'],
            ['nome' => 'Verona', 'codice_istat' => 'L780', 'cap' => '37100', 'sigla_provincia' => 'VR', 'regione' => 'Veneto'],
            ['nome' => 'Messina', 'codice_istat' => 'F158', 'cap' => '98100', 'sigla_provincia' => 'ME', 'regione' => 'Sicilia'],
            ['nome' => 'Padova', 'codice_istat' => 'G224', 'cap' => '35100', 'sigla_provincia' => 'PD', 'regione' => 'Veneto'],
            ['nome' => 'Trieste', 'codice_istat' => 'L424', 'cap' => '34100', 'sigla_provincia' => 'TS', 'regione' => 'Friuli-Venezia Giulia'],
            ['nome' => 'Brescia', 'codice_istat' => 'B157', 'cap' => '25100', 'sigla_provincia' => 'BS', 'regione' => 'Lombardia'],
            ['nome' => 'Reggio Calabria', 'codice_istat' => 'H224', 'cap' => '89100', 'sigla_provincia' => 'RC', 'regione' => 'Calabria'],
            ['nome' => 'Taranto', 'codice_istat' => 'L049', 'cap' => '74100', 'sigla_provincia' => 'TA', 'regione' => 'Puglia'],
            ['nome' => 'Modena', 'codice_istat' => 'F257', 'cap' => '41100', 'sigla_provincia' => 'MO', 'regione' => 'Emilia-Romagna'],
            ['nome' => 'Parma', 'codice_istat' => 'G337', 'cap' => '43100', 'sigla_provincia' => 'PR', 'regione' => 'Emilia-Romagna'],
            ['nome' => 'Reggio Emilia', 'codice_istat' => 'H223', 'cap' => '42100', 'sigla_provincia' => 'RE', 'regione' => 'Emilia-Romagna'],
            ['nome' => 'Perugia', 'codice_istat' => 'G478', 'cap' => '06100', 'sigla_provincia' => 'PG', 'regione' => 'Umbria'],
            ['nome' => 'Ravenna', 'codice_istat' => 'H199', 'cap' => '48100', 'sigla_provincia' => 'RA', 'regione' => 'Emilia-Romagna'],
            ['nome' => 'Livorno', 'codice_istat' => 'E625', 'cap' => '57100', 'sigla_provincia' => 'LI', 'regione' => 'Toscana'],
            ['nome' => 'Cagliari', 'codice_istat' => 'B354', 'cap' => '09100', 'sigla_provincia' => 'CA', 'regione' => 'Sardegna'],
            ['nome' => 'Foggia', 'codice_istat' => 'D643', 'cap' => '71100', 'sigla_provincia' => 'FG', 'regione' => 'Puglia'],
            ['nome' => 'Rimini', 'codice_istat' => 'H294', 'cap' => '47900', 'sigla_provincia' => 'RN', 'regione' => 'Emilia-Romagna'],
            ['nome' => 'Salerno', 'codice_istat' => 'H703', 'cap' => '84100', 'sigla_provincia' => 'SA', 'regione' => 'Campania'],
            ['nome' => 'Ferrara', 'codice_istat' => 'D548', 'cap' => '44100', 'sigla_provincia' => 'FE', 'regione' => 'Emilia-Romagna'],
            ['nome' => 'Sassari', 'codice_istat' => 'I452', 'cap' => '07100', 'sigla_provincia' => 'SS', 'regione' => 'Sardegna'],
            ['nome' => 'Latina', 'codice_istat' => 'E472', 'cap' => '04100', 'sigla_provincia' => 'LT', 'regione' => 'Lazio'],
            ['nome' => 'Giugliano in Campania', 'codice_istat' => 'E045', 'cap' => '80014', 'sigla_provincia' => 'NA', 'regione' => 'Campania'],
            ['nome' => 'Monza', 'codice_istat' => 'F704', 'cap' => '20900', 'sigla_provincia' => 'MB', 'regione' => 'Lombardia'],
            ['nome' => 'Siracusa', 'codice_istat' => 'I754', 'cap' => '96100', 'sigla_provincia' => 'SR', 'regione' => 'Sicilia'],
            ['nome' => 'Bergamo', 'codice_istat' => 'A794', 'cap' => '24100', 'sigla_provincia' => 'BG', 'regione' => 'Lombardia'],
            ['nome' => 'Pescara', 'codice_istat' => 'G482', 'cap' => '65100', 'sigla_provincia' => 'PE', 'regione' => 'Abruzzo'],
            ['nome' => 'Forlì', 'codice_istat' => 'D704', 'cap' => '47100', 'sigla_provincia' => 'FC', 'regione' => 'Emilia-Romagna'],
            ['nome' => 'Trento', 'codice_istat' => 'L378', 'cap' => '38100', 'sigla_provincia' => 'TN', 'regione' => 'Trentino-Alto Adige'],
            ['nome' => 'Terni', 'codice_istat' => 'L117', 'cap' => '05100', 'sigla_provincia' => 'TR', 'regione' => 'Umbria'],
            ['nome' => 'Novara', 'codice_istat' => 'F952', 'cap' => '28100', 'sigla_provincia' => 'NO', 'regione' => 'Piemonte'],
            ['nome' => 'Ancona', 'codice_istat' => 'A271', 'cap' => '60100', 'sigla_provincia' => 'AN', 'regione' => 'Marche'],
            ['nome' => 'Piacenza', 'codice_istat' => 'G535', 'cap' => '29100', 'sigla_provincia' => 'PC', 'regione' => 'Emilia-Romagna'],
            ['nome' => 'Lecce', 'codice_istat' => 'E506', 'cap' => '73100', 'sigla_provincia' => 'LE', 'regione' => 'Puglia'],
            ['nome' => 'Bolzano', 'codice_istat' => 'A952', 'cap' => '39100', 'sigla_provincia' => 'BZ', 'regione' => 'Trentino-Alto Adige'],
            ['nome' => 'Catanzaro', 'codice_istat' => 'C352', 'cap' => '88100', 'sigla_provincia' => 'CZ', 'regione' => 'Calabria'],
            ['nome' => 'Udine', 'codice_istat' => 'L483', 'cap' => '33100', 'sigla_provincia' => 'UD', 'regione' => 'Friuli-Venezia Giulia'],
            ['nome' => 'Aversa', 'codice_istat' => 'A512', 'cap' => '81031', 'sigla_provincia' => 'CE', 'regione' => 'Campania'],
            ['nome' => 'Potenza', 'codice_istat' => 'G942', 'cap' => '85100', 'sigla_provincia' => 'PZ', 'regione' => 'Basilicata'],
            ['nome' => 'Castellammare di Stabia', 'codice_istat' => 'C129', 'cap' => '80053', 'sigla_provincia' => 'NA', 'regione' => 'Campania'],
            ['nome' => 'Afragola', 'codice_istat' => 'A064', 'cap' => '80021', 'sigla_provincia' => 'NA', 'regione' => 'Campania'],
            ['nome' => 'Crotone', 'codice_istat' => 'D122', 'cap' => '88900', 'sigla_provincia' => 'KR', 'regione' => 'Calabria'],
            ['nome' => 'Pavia', 'codice_istat' => 'G388', 'cap' => '27100', 'sigla_provincia' => 'PV', 'regione' => 'Lombardia'],
            ['nome' => 'Caserta', 'codice_istat' => 'B963', 'cap' => '81100', 'sigla_provincia' => 'CE', 'regione' => 'Campania'],
            ['nome' => 'Salò', 'codice_istat' => 'H727', 'cap' => '25087', 'sigla_provincia' => 'BS', 'regione' => 'Lombardia'],
            ['nome' => 'Brescia', 'codice_istat' => 'B157', 'cap' => '25100', 'sigla_provincia' => 'BS', 'regione' => 'Lombardia'],
        ];

        foreach ($comuni as $c) {
            Comune::create($c);
        }
    }
}