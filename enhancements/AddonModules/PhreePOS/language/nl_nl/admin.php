<?php
// +-----------------------------------------------------------------+
// Phreedom Language Translation File
// Generated: 2011-11-27 05:02:36
// Module/Method: phreepos
// ISO Language: nl_nl
// Version: 1.2
// +-----------------------------------------------------------------+
// Path: /modules/phreepos/language/nl_nl/admin.php

define('MODULE_PHREEPOS_TITLE','PhreePOS Module');
define('TEXT_PHREEPOS_SETTINGS', 'PhreePOS Module Instellingen');
define('MODULE_PHREEPOS_DESCRIPTION','De PhreePOS module biedt een kassa / Point of Sale-interface Deze module is een aanvulling op de phreebooks module en is geen vervanging voor.');
define('BOX_PHREEPOS_ADMIN','Kassa / Point of Sale Administration');
define('PHREEPOS_REQUIRE_ADDRESS_DESC','Is adres informatie bij iedere handeling vereist');
define('PHREEPOS_RECEIPT_PRINTER_NAME_DESC','Stelt de naam van de printer in te gebruiken voor het afdrukken van kassa bonnen, zoals gedefinieerd in de printer voorkeuren voor het lokale werkstation');
define('PHREEPOS_RECEIPT_PRINTER_STARTING_LINE_DESC','Hier kunt u code invoeren om die aan het begin van de pagina hoort <br> Scheid de codes door een: en regels en door een , zoals: <i> 27:112:48:55:121,27:109 </ i> <br> De codes zijn een nummers van de chr dwz chr (13) is 13 <b> Plaats hier alleen de code geen tekst dit kan leiden tot fouten. </ b> Bekijk de printer documentatie voor de codes.');
define('PHREEPOS_RECEIPT_PRINTER_CLOSING_LINE_DESC','Hier kunt u code invoeren om lade te openen en / of bon af te snijden <br> Scheid de codes door een: en regels en door een , zoals: <i> 27:112:48:55:121,27:109 </ i> <br> De codes zijn een nummers van de chr dwz chr (13) is 13 <b> Plaats hier alleen de code geen tekst dit kan leiden tot fouten. </ b>');
//3.3
define('PHREEPOS_RECEIPT_PRINTER_OPEN_DRAWER_DESC','Hier kunt u code invoeren om lade te openen betaalvorm afhankelijk (stel de open lade optie in onder de betaal methodes).<br> als een van de geselecteerde betaal methodes de optie open lade op waar in heeft gesteld dan wordt de lade geopend.<br> Scheid de codes door een: en regels en door een , zoals: <i> 27:112:48:55:121,27:109 </ i> <br> De codes zijn een nummers van de chr dwz chr (13) is 13 <b> Plaats hier alleen de code geen tekst dit kan leiden tot fouten. </ b>');
define('TEXT_ENTER_NEW_TILL','Nieuwe Kassa Lade');
define('TEXT_EDIT_TILL','Bewerk Kassa Lade');
define('TEXT_TILLS','Kassa Lade');
define('SETUP_TILL_DELETE_INTRO','Wil u deze kassa lade verwijderen?');
define('PHREEPOS_DISPLAY_WITH_TAX_DESC','Wil u de prijzen op het scherm laten zien met btw<br> (als u nee selecteerd worden de prijzen exclusief btw)');
define('PHREEPOS_DISCOUNT_OF_DESC','wilt u dat de korting wordt berekend over het totaal<br> ( als u nee selecteerd wordt de korting berekend over het sub totaal) ');
define('PHREEPOS_ROUNDING_DESC','Hoe wil u dat het eindtotaal wordt afgerond.<br> <b>NEE</b> betekend dat het eindtotaal niet wordt afgerond.<br><b>GEHEEL GETAL</b> betekend:  dat naar de eerste euro omlaag wordt afgerond.<br><b>10 CENTEN</b> Betekend:  dat alles naar de eerste 10cent omlaag wordt afgerond.<br><b>NEUTRAAL</b> Er zal worden afgerond naar de dichts bij zijnde 0, 5 of 10 cent (1,2,6,7 naar beneden 3,4,8,9 naar boven)');
define('TEXT_INTEGER','geheel getal');
define('TEXT_10_CENTS','10 Centen');
define('TEXT_NEUTRAL','Neutraal');
define('TEXT_ROUNDING_OF','Afrounden');
define('TEXT_GL_ACCOUNT_ROUNDING','Grootboek rekening voor afronden:');

?>
