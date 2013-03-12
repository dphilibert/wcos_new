<?php
declare(ENCODING = 'utf-8');

/**
 * @author Michael Fischwer
 * @version $Id$ 
 */
class Wcos
{
	protected $user	= 'mfischer';
	protected $pass	= '8f8X6WXHnATcM62Y';
	protected $server	= 'localhost';
	protected $dbase	= 'wcos';
	protected $counter;
	
	public function __construct()
	{
		$conn = mysql_connect($this->server,$this->user,$this->pass);
		if(!$conn) {
			$this->error("Verbindungsfehler ");
		}
		if(!mysql_select_db($this->dbase,$conn)) {
			$this->error("Datenbankfehler");
		}
		$this->CONN = $conn;
	}

	public function select ($sql)
	{
		if(empty($sql)) { return false; }
		
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$results = mysql_query($sql,$conn);
		return $results;
	}
	
	public function processDeletion($importTabelle)
	{
		$anbieter_datei	= fopen('sicherung/'.date('Y-m-d-H-i-s',time()).'_anbieter.sql','wb');
		$stammdaten_datei	= fopen('sicherung/'.date('Y-m-d-H-i-s',time()).'_stammdaten.sql','wb');
		
		$sql = "SELECT * from anbieter";
		$res = $this->select($sql);
		$i = 0;
		while ($row = mysql_fetch_assoc($res))
		{
			if ($this->checkKundenNummer($importTabelle, $row['anbieterID']) == false)
			{
				// zu löschende Anbieter sichern
				$this->backupAnbieter($row,$anbieter_datei);
				
				// zu löschende Stammdaten sichern
				$this->backupStammdaten($this->getStammdaten($row['stammdatenID']),$stammdaten_datei);
				
				// Anbieter löschen
				$this->deleteAnbieter($row['anbieterID']);
				
				// Stammdaten löschen
				$this->deleteStammdaten($row['stammdatenID']);
				
				$i++;
			}
		}
		
		fclose ($anbieter_datei);
		fclose ($stammdaten_datei);
		return $i;
	}
	
	/**
	 * Aktualisierung der Daten in der anbieter-Tabelle
	 * 
	 * Die Daten aus der Import-Tabelle werden ausgelesen und mit den Daten 
	 * in der anbieter-Tabelle verglichen. Unterscheiden sich die Daten der 
	 * beiden Tabellen werden die Werte aus der Import-Tabelle in die 
	 * Anbieter-Tabelle geschrieben, nicht in der Anbieter-Tabelle vorhandene 
	 * Firnem werden neu angelegt.
	 * 
	 * @return array diverse Zähler
	 */
	public function processAnbieterUpdate($importTabelle)
	{
		$sql = "SELECT * FROM ".$importTabelle."";
		$res = $this->select($sql);
		
		// Name der Anbieter-Tabelle im WCOS
		$tabelle = 'anbieter';
		
		// Felder für den Abgleich
		$feldNamen = array('firmenname','name1','name2','name3','Suchname');
		
		// Zähler für die Anzahl der Datensätze
		$this->counter['i'] = 0;
		// Zähler für die Anzahl der Aktualisierungen
		$this->counter['n'] = 0;
		// Zähler für die Anzahl der Neuanlagen
		$this->counter['m'] = 0;
		
		while ($row = mysql_fetch_assoc($res))
		{
			$row['firmenname'] = $row['name1'].' '.$row['name2'].' '.$row['name3'];

			foreach ($feldNamen as $feld)
			{
				$feldInhalt = $this->getFeldInhalt($feld,$tabelle,$row['kunden_nr']);
				
				if ($this->checkAnbieterID($tabelle, $row['kunden_nr']) == true)
				{
					if (trim($row[$feld]) != $feldInhalt)
					{
						$this->updateTable($tabelle,$feld,$row[$feld],$row['kunden_nr']);
						$this->counter['n']++;
					}
				}
				else
				{
					$this->addAnbieter($row);
					$this->counter['m']++;
				}
			}
			
			// Nur damit sich in der Cinsole während der verarbeitung etwas tut
			print '.';
			
			$this->counter['i'] ++;
		}
		print '.'."\n";
				
		return $this->counter;
	}
	
	/**
	 * Aktualisierung der Daten in der stammdaten-Tabelle
	 * 
	 * Die Daten aus der Import-Tabelle werden ausgelesen und mit den Daten 
	 * in der stammdaten-Tabelle verglichen. Unterscheiden sich die Daten der 
	 * beiden Tabellen werden die Werte aus der Import-Tabelle in die 
	 * stammdaten-Tabelle geschrieben.
	 * 
	 * @return array diverse Zähler
	 */
	public function processStammdatenUpdate($importTabelle)
	{
		$sql = "SELECT * FROM ".$importTabelle."";
		$res = $this->select($sql);
		
		// Name der Stammdatentabelle im WCOS
		$tabelle = 'stammdaten';
		
		// Felder für den Abgleich
		$feldNamen = array('strasse','hausnummer','land','plz','ort','fon','fax','email','www');
		
		// Zähler für die Anzahl der Datensätze
		$this->counter['i'] = 0;
		// Zähler für die Anzahl der Aktualisierungen
		$this->counter['n'] = 0;
		
		while ($row = mysql_fetch_array($res))
		{
			foreach ($feldNamen as $feld)
			{
				$feldInhalt = $this->getFeldInhalt($feld,$tabelle,$row['kunden_nr']);
				
				if (($this->checkAnbieterID($tabelle, $row['kunden_nr']) == true) && (trim($row[$feld]) != $feldInhalt))
				{
					$this->updateTable($tabelle,$feld,$row[$feld],$row['kunden_nr']);
					
					$this->counter['n']++;
				}
			}
			// Nur damit sich in der Cinsole während der verarbeitung etwas tut
			print '.';
			$this->counter['i']++;
		}
		
		print '.'."\n";
		
		return $this->counter;
	}

	/**
	 * Gibt den Inhalt des angegebenen Feldes für den angegebenen Anbieter aus 
	 * angegebene Tabelle.
	 * 
	 * @param string $feld Feldname für Inhaltsabfrage
	 * @param string $tabelle Tabelle für die Inhaltsabfrage
	 * @param int $anbieterID Kundennummer des Anbieters
	 * @return string Der Feldinhalt
	 */
	private function getFeldInhalt($feld,$tabelle,$anbieterID)
	{
		$sql = "SELECT ".$feld." from ".$tabelle." WHERE anbieterID = '".$anbieterID."' Limit 1";
		
		$res = $this->select($sql);
		
		$arr = mysql_fetch_array($res);
		
		return trim($arr[$feld]);
	}
	
	
	/**
	 * Prüft ob eine  in der angegebenen Tabelle vorhanden ist. 
	 * 
	 * @param string $tabelle Name der Tabelle
	 * @param int $anbieterID Kundennummer des Anbieters
	 * @return boolean 
	 */
	private function checkAnbieterID($tabelle,$anbieterID)
	{
		$sql = "SELECT anbieterID from ".$tabelle." WHERE anbieterID = '".$anbieterID."' Limit 1";
		$res = $this->select($sql);
		$rows = mysql_num_rows($res);
		usleep(1);
		if ($rows == 1){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Prüft ob eine Kundennummer in der Importtabelle vorhanden ist. 
	 * 
	 * @param string $tabelle Name der Tabelle
	 * @param int $anbieterID Kundennummer des Anbieters
	 * @return boolean 
	 */
	private function checkKundenNummer($importTabelle,$anbieterID)
	{
		$sql = "SELECT kunden_nr from ".$importTabelle." WHERE kunden_nr = '".$anbieterID."' Limit 1";
		$res = $this->select($sql);
		$rows = mysql_num_rows($res);
		usleep(1);
		if ($rows == 1){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Speichert die Aktualisierungen für den angegebenen Kunden in die 
	 * angegebene Tabelle.
	 * 
	 * @param string $tabelle Die Tabelle in der die Aktualisierung vorgenommen wird
	 * @param string $feld Das zu aktualisierende Feld
	 * @param string $wert Der Wert für oben das angegebene Feld
	 * @param int $anbieterID Die Kundennummer
	 * @return void
	 */
	private function updateTable($tabelle,$feld,$wert,$anbieterID)
	{
		$sql = "UPDATE ".$tabelle." SET ".$feld." = '".trim(mysql_escape_string($wert))."' WHERE anbieterID = ".$anbieterID."";
		$res = $this->select($sql);
		usleep(1);
	}
	
	
	/**
	 * Legt einen neuen Anbieter in der WCOS anbieterTabelle an.
	 * 
	 * @param array $row Array mit den AnbieterDaten
	 * @return void
	 */
	private function addAnbieter($row)
	{
		// Anbieterdaten in anbieter_Tabelle neu anlegen
		$sql_a = "INSERT INTO anbieter SET
					anbieterID		=	'".$row['kunden_nr']."',
					systems			=	'1',
					companyID		=	'".$row['nummer']."',
					stammdatenID	=	'".$row['nummer']."',
					firmenname		=	'".trim($row['name1'])." ".trim($row['name2'])." ".trim($row['name3'])."',
					name1			=	'".trim($row['name1'])."',
					name2			=	'".trim($row['name2'])."',
					name3			=	'".trim($row['name3'])."',
					anbieterhash	=	'".md5($row['nummer'])."',
					number			=	'".$row['kunden_nr']."',
					LebenszeitID	=	'".$row['nummer']."',
					Suchname		=	'".$row['Suchname']."',
					created			=	'".date('Y-m-d H:i:s', time())."'
				";
		$res_a = $this->select($sql_a);
		
		// anbieterID und stammdatenID in stammdatenTabelle ietragen.
		// Weitere Daten müpssen zu dem Zeitpunkt noch nicht vorgenommen werden, 
		// da die Anreicherung der Stammdaten via processStammdatenUpdate
		//  erfolgt.
		$sql_s = "INSERT INTO stammdaten SET
					anbieterID		=	'".$row['kunden_nr']."',
					stammdatenID	=	'".$row['nummer']."'
				";
		$res_s = $this->select($sql_s);
	}

	private function backupAnbieter($anbieter,$datei)
	{
		$string = 'INSERT INTO anbieter SET 
			id				=	"'.$anbieter['id'].'",
			anbieterID		=	"'.$anbieter['anbieterID'].'",
			systems			=	"'.$anbieter['systems'].'",
			companyID		=	"'.$anbieter['companyID'].'",
			stammdatenID	=	"'.$anbieter['stammdatenID'].'",
			firmenname		=	"'.$anbieter['firmenname'].'",
			name1			=	"'.$anbieter['name1'].'",
			name2			=	"'.$anbieter['name2'].'",
			name3			=	"'.$anbieter['name3'].'",
			name4			=	"'.$anbieter['name4'].'",
			anbieterhash	=	"'.$anbieter['anbieterhash'].'",
			premiumLevel	=	"'.$anbieter['premiumLevel'].'",
			last_login		=	"'.$anbieter['last_login'].'",
			number			=	"'.$anbieter['number'].'",
			LebenszeitID	=	"'.$anbieter['LebenszeitID'].'",
			Suchname		=	"'.$anbieter['Suchname'].'",
			lastChange		=	"'.$anbieter['lastChange'].'",
			created			=	"'.$anbieter['created'].'";'."\n\n";
				
		fputs ($datei, $string);
	}
	
	private function deleteAnbieter($anbieterID)
	{
		$sql = "DELETE FROM anbieter WHERE anbieterID = ".$anbieterID."";
		$res = $this->select($sql);
	}
	
	private function getStammdaten($stammdatenID)
	{
		$sql = "SELECT * FROM stammdaten WHERE stammdatenID = ".$stammdatenID."";
		$res = $this->select($sql);
		$row = mysql_fetch_assoc($res);
		return $row;
	}
	
	private function deleteStammdaten($stammdatenID)
	{
		$sql = "DELETE FROM stammdaten WHERE stammdatenID = ".$stammdatenID."";
		$res = $this->select($sql);
	}
	
	private function backupStammdaten($stammdaten,$datei)
	{
		$string = 'INSERT INTO stammdaten SET 
			stammdatenID	=	"'.$stammdaten['stammdatenID'].'",
			anbieterID		=	"'.$stammdaten['anbieterID'].'",
			userID			=	"'.$stammdaten['userID'].'",
			strasse			=	"'.$stammdaten['strasse'].'",
			hausnummer		=	"'.$stammdaten['hausnummer'].'",
			land			=	"'.$stammdaten['land'].'",
			plz				=	"'.$stammdaten['plz'].'",
			ort				=	"'.$stammdaten['ort'].'",
			fon				=	"'.$stammdaten['fon'].'",
			fax				=	"'.$stammdaten['fax'].'",
			email			=	"'.$stammdaten['email'].'",
			www				=	"'.$stammdaten['www'].'",
			mediaID			=	"'.$stammdaten['mediaID'].'";'."\n\n";
				
		fputs ($datei, $string);
	}
}
?>
