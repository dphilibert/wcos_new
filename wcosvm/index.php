<?php
/**
 * Abgleich ders VM Kundenimportes mit den WCOS-Tabellen 
 */

// Datenbankverbindung herstellen
include 'class.wcos.php';
$model = new Wcos();

$cliParameter = getopt('i:');

$importTabelle = 'vm_kunden_import';

switch($cliParameter['i'])
{
	
	/**
	 * Abgleich der Importdaten mit den Daten der Anbieter-Tabelle
	 */
	case 'anbieter':
		$ergebnis = $model->processAnbieterUpdate($importTabelle);
		echo $ergebnis['i']. ' Anbietern in Importtabelle vorhanden'."\n";
		echo $ergebnis['n']. ' Felder von '.$ergebnis['i'].' Anbietern aktualisiert'."\n";
		echo $ergebnis['m']. ' Anbieter in neu WCOS angelegt'."\n";
	break;

	/**
	 * Abgleich der Importdaten mit den Daten der Stammdaten-Tabelle 
	 */
	case 'stammdaten':
		$ergebnis = $model->processStammdatenUpdate($importTabelle);
		echo $ergebnis['n']. ' Felder von '.$ergebnis['i'].' Stammdatensaetzen aktualisiert'."\n";
	break;

	/**
	 * Löschung der Firmen, die nicht in der Importdatei vorhanden sind 
	 */
	case 'delete':
		$ergebnis = $model->processDeletion($importTabelle);
		echo $ergebnis .' Datensaetze geloescht.'."\n";
	break;
	
}



?>
