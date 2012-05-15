<?php

  /**
   *
   * Datenbank-Model für die Anbieter-Daten
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   *
   **/
  class Model_DbTable_AnbieterData extends Zend_Db_Table_Abstract
  {
    protected $csvFileName = "kunden.csv";
    protected $csvPath = "../_files/";


    /**
     *
     * Anbieter suchen anhand eines Suchbegriffs
     *
     * @param string $searchPhrase Suchbegriff
     *
     * @return mixed
     *
     */
    public function searchAnbieter ($searchPhrase, $systemID = NULL)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      /*
          $select->from (array ('u' => 'user'))
                 ->join (array ('ud' => 'userDetails'), 'u.userDetailID = ud.userDetail_ID')
                 ->join (array ('land' => 'laender'), 'ud.landID = land.laenderID')
                 ->where ('ud.firmenname like "'.$searchPhrase.'%"', $searchPhrase);
      */
      //           ->orWhere ('ud.nachname like "'.$searchPhrase.'%"', $searchPhrase)
      //           ->orWhere ('ud.vorname like "'.$searchPhrase.'%"');
      $keywords = explode (' ', $searchPhrase);
      $select->from (array('a' => 'anbieter'));
      foreach ($keywords as $keyword)
      {
        $select->orWhere ("a.firmenname like '%$keyword%'");
      }
      $select->join (array('sd' => 'stammdaten'), 'a.stammdatenID = sd.stammdatenID');
      //LEFT JOIN media AS m ON m.anbieterID=a.anbieterID AND m.mediatyp='FIRMENLOGO'
      $select->joinLeft (array ('m' => 'media'), 'a.anbieterID = m.anbieterID AND m.mediatyp="FIRMENLOGO"');
      //->where ('a.firmenname like "' . $searchPhrase . '%"')
      $select->group ('a.anbieterID');
      $select->order (array('a.firmenname ASC'));

      //logDebug (print_r ($select->__toString (), true), "");
      if ($systemID != NULL)
      {
        $select->where ("a.systems like '%$systemID%'");
      }
      //logDebug (print_r ($select->__toString (), true), "");
      $result = $select->query ();
      $data = $result->fetchAll ();
      //array_walk_recursive ($data, 'utfEncode');
      //array_walk_recursive ($data, 'utfEncode');
      $i = 0;
      if (count ($data) > 0)
      {
        foreach ($data as $key => $hit)
        {
          $i++;
          $anbieter ['hits'] [$key] = $hit;
        }
      }
      if ($i > 0)
      {
        return $anbieter;
      }
      return NULL;
      /*
      $operator = 'AND';
         $keywords = explode(" ",$phrase);
         $where = array();
         foreach($keywords as $keyword) {
          $where[] = "(`name`LIKE '%".$keyword."%' OR  `name_zusatz` LIKE '%".$keyword."%' )";
         }
         $where = implode("\n ".$operator." ",$where);
        
         $sql = "SELECT  * FROM `anbieter_company` WHERE   ".$where." ";
      */
    }


    /**
     *
     * gibt eine Anbieterliste für das entsprechende System zurück
     *
     * @param int $systemID systemID
     *
     * @return mixed
     *
     */
    public function getAnbieterList ($systemID = 1)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('a' => 'anbieter'))
      //->where ('a.systemID = ?', $systemID) // TODO systems abfragen
      ->order ('a.firmenname ASC');
      // ->limit (10);
      $result = $select->query ();
      $data = $result->fetchAll ();
      //array_walk_recursive ($data, 'utfEncode');
      return $data;
    }

    /**
     *
     * liefert die Details zu einem Anbieter
     * Tabellen: stammdaten, laufzeiten
     *
     * @param int $anbieterID anbieterID
     *
     * @return array
     * @throws Zend_Exception
     *
     */
    public function getAnbieterDetails ($anbieterID = NULL)
    {
      if ($anbieterID == NULL)
      {
        throw new Zend_Exception ("Model_DbTable_Anbieter::getAnbieterDetails - ungülige anbieterID");
      }
      $anbieter = NULL;
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('a' => 'anbieter'))
      ->join (array('sd' => 'stammdaten'), 'sd.stammdatenID = a.stammdatenID')
      ->join (array('lz' => 'laufzeiten'), 'lz.anbieterID = a.anbieterID')
      ->where ('a.anbieterID = ? ', $anbieterID);
      $result = $select->query ();
      $data = $result->fetch ();
      //array_walk_recursive ($data, 'utfEncode');
      if (is_array ($data))
      {
        foreach ($data as $key => $value)
        {
          $key = strtoupper ($key);
          $anbieter [$key] = $value;
        }
      }
      return $anbieter;
    }


    /**
     *
     * liefert den Anbieter-Datensatz (Tabellen anbieter und user2anbieter) für eine anbieterID
     *
     * @param int $anbieterID anbieterID
     *
     * @return array
     *
     */
    public function getAnbieter ($anbieterID)
    {
      $anbieter = NULL;
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('a' => 'anbieter'))
      ->where ('a.anbieterID = ? ', $anbieterID);
      $result = $select->query ();
      $data = $result->fetch ();
      $select = $db->select ();
      $select->from (array('u2a' => 'user2anbieter'))
      ->where ('u2a.anbieterID = ? ', $anbieterID);
      $result = $select->query ();
      $data ['user'] = $result->fetchAll ();
      return $data;
    }

    /**
     * liefert die angegebene Zahl von zufälligen Accounts
     *
     * @param $anbieterID
     *
     * @return array
     */
    public function getAnbieterRandom ($systemID, $anzahlDerEintraege)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('a' => 'anbieter'))
      ->join (array('sd' => 'stammdaten'), 'a.stammdatenID = sd.stammdatenID')
      ->where ('a.premiumLevel = ?', 1)
      ->order (array('RAND()'))
      ->limit ($anzahlDerEintraege);
      //logDebug (print_r ($select->__toString ()), "");
      if ($systemID != NULL)
      {
        $select->where ("a.systems like ?", '%' . $systemID . '%');
      }
      try
      {
        $result = $select->query ();
      } catch (Zend_Exception $e)
      {
        logError ($e->getMessage (), "");
      }
      $data = $result->fetchAll ();
      //array_walk_recursive ($data, 'utfEncode');
      $i = 0;
      if (count ($data) > 1)
      {
        foreach ($data as $key => $hit)
        {
          $i++;
          $anbieter ['hits'] [$key] = $hit;
        }
      }
      if ($i > 0)
      {
        return $anbieter;
      }
      return NULL;
    }

    /**
     *
     * liefert den Anbieter-Datensatz (Tabelle anbieter) für einen Anbieter-Hash
     *
     * @param string $hash Anbieter-Hash
     *
     * @return mixed
     */
    public function getAnbieterByHash ($hash)
    {
      $anbieter = NULL;
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('a' => 'anbieter'))
      ->where ('a.anbieterHash = ? ', $hash);
      $result = $select->query ();
      $data = $result->fetch ();
      return $data;
    }

    /**
     *
     * liefert die limits (Tabelle limits) für eine anbieterID
     *
     * @param int $anbieterID anbieterID
     *
     * @return object
     */
    public function getAnbieterLimits ($anbieterID)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('l' => 'limits'))
      ->where ('l.anbieterID = ?', $anbieterID)
      ->orWhere ('l.anbieterID = 0');
      $result = $select->query ();
      $data = $result->fetchAll ();
      return (object)$data [0];
    }


    /**
     *
     * importiert Anbieter-Daten aus einem Dataset in die Anbieter-Tabelle
     *
     * @param array $data Dataset
     */
    public function importAnbieterData ($data)
    {
      $companyID = $data ['company_id'];
      $anbieterID = $data ['wcid'];
      $strasse = utf8_decode ($data ['street']);
      $strasse = str_replace ("'", "\'", $strasse);
      $hausnummer = utf8_decode ($data ['hausnummer']);
      $plz = utf8_decode ($data ['zipcode']);
      $plz = str_replace ("'", "\'", $plz);
      $ort = utf8_decode ($data ['city']);
      $ort = str_replace ("'", "\'", $ort);
      $fon = utf8_decode ($data ['telephone']);
      $fax = utf8_decode ($data ['fax']);
      $email = utf8_decode ($data ['email']);
      $www = utf8_decode ($data ['www']);
      $land = utf8_decode ($data ['country']);
      $firmenname = utf8_decode ($data ['name']);
      $firmenname = str_replace ("'", "\'", $firmenname);
      $db = Zend_Registry::get ('db');
      $sql = "INSERT INTO stammdaten (userID, strasse, hausnummer, land, plz, ort, fon, fax, email, www) VALUES
                                   (0, '$strasse', '$hausnummer', '$land', '$plz', '$ort', '$fon', '$fax', '$email', '$www')";
      //    //logDebug ($sql, $lastID);
      $db->query ($sql);
      $lastID = $db->lastInsertId ();
      $sql = "INSERT INTO anbieter (anbieterID, systemID, companyID, stammdatenID, firmenname) VALUES
                                 ($anbieterID, 1, $companyID, $lastID, '$firmenname')";
      //    //logDebug ($sql, $lastID);
      $db->query ($sql);
    }


    /**
     *
     * synchronisiert die Anbieter-Daten
     *
     * @deprecated
     *
     * @param array $data Dataset
     */
    public function syncAnbieterData ($data)
    {
    }


    /**
     *
     * liefert einen Anbieter-Datensatz zu einer lokalen userID (kein ZBVS-User)
     *
     * @param int $userID userID
     *
     * @return mixed
     */
    public function getAnbieterByUser ($userID)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('u2a' => 'user2anbieter'));
      $select->where ("u2a.userID = ?", $userID);
      $result = $select->query ();
      $data = $result->fetchAll ();
      return $data [0];
    }

    /**
     *
     * liefert einen Anbieter-Datensatz zu einer Kundennummer (Tabelle anbieter)
     *
     * @param int $firmaKundennummer Kundennummer
     *
     * @return mixed
     */
    public function getAnbieterByKundennummer ($firmaKundennummer)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('a' => 'anbieter'));
      $select->where ("a.number = ?", $firmaKundennummer);
      $result = $select->query ();
      $data = $result->fetch ();
      return $data;
    }

    /**
     * liefert die zuletzt geänderten Anbieter
     *
     * @param $systemID
     * @param $limit
     *
     * @return mixed
     */
    public function getLastChanged ($systemID, $limit)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('a' => 'anbieter'));
      if ($systemID > 0)
      {
        $select->where ("a.systems like '%$systemID%'");
      }
      $select->order ("lastChange desc")
      ->limit ($limit);
      try
      {
        $result = $select->query ();
      } catch (Zend_Exception $e)
      {
        logError ($e->getMessage (), "");
      }
      $data = $result->fetchAll ();
      return $data;
    }

    /**
     * liefert die neuesten Anbieter (created)
     *
     * @param $systemID
     * @param $limit
     *
     * @return mixed
     */
    public function getNewest ($systemID, $limit)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('a' => 'anbieter'));
      if ($systemID > 0)
      {
        $select->where ("a.systems like '%$systemID%'");
      }
      $select->order ("created desc")
      ->limit ($limit);
      try
      {
        $result = $select->query ();
      } catch (Zend_Exception $e)
      {
        logError ($e->getMessage (), "");
      }
      $data = $result->fetchAll ();
      return $data;
    }

    /**
     * liefert die meist gesehenen Anbieter
     *
     * @param $systemID
     * @param $limit
     *
     * @return mixed
     */
    public function getMostSeen ($systemID, $limit)
    {
      $db = Zend_Registry::get ('db');
      $sql = "select vmKundennummer, count(*) as anzahl from stats_visits group by vmKundennummer order by anzahl desc limit $limit";
      try
      {
        $result = $db->query ($sql);
      } catch (Zend_Exception $e)
      {
        logError ($e->getMessage (), "");
      }
      $data = $result->fetchAll ();
      return $data;
    }

    /**
     *
     * speichert Anbieter-Daten (Feld, Wert) zu einer anbieterID (aID)
     *
     * @static
     *
     * @param string $field DB-Feldname
     * @param string $value Wert
     * @param int $aID anbieterID
     *
     * @return int
     *
     */
    public static function saveAnbieter ($field, $value, $aID)
    {
      $db = Zend_Registry::get ('db');
      if ($field != NULL) // nur ein Feld des Users speichern
      {
        $tableName = "anbieter";
        $whereCond = "anbieterID = $aID";
      }
      $data = array($field => $value);
      try
      {
        $n = $db->update ($tableName, $data, $whereCond);
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "AnbieterData::saveAnbieter");
        logError ("Table: $tableName / Where: $whereCond / Data: " . print_r ($data, true), "AnbieterData::saveAnbieter");
        return 2; // Return-Code ==> Fehler beim Speichern
      }
      return 0; // Return-Code ==> Speichern erfolgreich
    }


    /**
     *
     * liest eine Zeile mit einer Anzahl von Feldern ein (CSV-Funktion)
     *
     * @param resource $fileHandle
     * @param string $lineDelimiterASCII
     * @param int $felderAnzahl
     *
     * @return string
     *
     */
    public function readln ($fileHandle, $lineDelimiterASCII, $felderAnzahl = NULL)
    {
      $line = NULL;
      // Satztrenner ignorieren wenn Field-Anzahl noch nicht erreicht
      $fieldCount = 0;
      if ($felderAnzahl == NULL)
      {
        while (ord ($c) != $lineDelimiterASCII)
        {
          $c = fgetc ($fileHandle);
          if (ord ($c) != $lineDelimiterASCII)
          {
            $line .= $c;
          }
        }
      }
      else
      {
        while ($fieldCount + 1 < $felderAnzahl)
        {
          $c = fgetc ($fileHandle);
          if (ord ($c) != $lineDelimiterASCII)
          {
            $line .= $c;
          }
          if ($c == "|")
          {
            $fieldCount++;
          }
        }
      }
      return $line;
    }


    /**
     *
     * importiert CSV-Daten in die vm_import_kunden Tabelle
     *
     * @return void
     *
     */
    public function importData ()
    {
      //logDebug ("Starte Import");
      ini_set ("max_execution_time", "3800");
      $db = Zend_Registry::get ('db');
      $fileName = $this->csvPath . $this->csvFileName;
      $fileHandle = fopen ($this->csvPath . $this->csvFileName, "r");
      $firstLine = $this->readln ($fileHandle, 240); // erste Zeile mit ASCII 240 als Zeilentrenner
      $firstLine = strtolower ($firstLine);
      $firstLine = str_replace (' ', '_', $firstLine);
      $firstLine = str_replace ('-', '_', $firstLine);
      $firstLine = str_replace (chr (228), 'ae', $firstLine);
      $firstLine = str_replace (chr (246), 'oe', $firstLine);
      $firstLine = str_replace (chr (252), 'ue', $firstLine);
      $headerFields = explode ("|", $firstLine);
      $anzahlHeaderFelder = count ($headerFields);
      $i = 0;
      while (!feof ($fileHandle) && $i < 10000000)
      {
        $i++;
        $line = $this->readln ($fileHandle, 254, $anzahlHeaderFelder); // alle weiteren Zeilen mit ASCII 254 als Zeilentrenner
        $line = str_replace ("'", " ", $line);
        $line = str_replace ("/", "-", $line);
        $line = str_replace (chr (141), " ", $line);
        $fields = explode ("|", $line);
        $anzahlFelder = count ($fields);
        $sql = "INSERT INTO vm_import_kunden (";
        foreach ($headerFields as $key => $field)
        {
          $sql .= $field;
          if (($key + 1) < $anzahlHeaderFelder)
          {
            $sql .= ",";
          }
        }
        $sql .= ") VALUES (";
        foreach ($fields as $key => $field)
        {
          $sql .= "'" . $field . "'";
          if (($key + 1) < $anzahlFelder)
          {
            $sql .= ",";
          }
        }
        $sql .= ")";
        if ($anzahlHeaderFelder != $anzahlFelder)
        {
          //logDebug ($sql, "fatal error: Unterschiedliche Feldanzahl ($anzahlHeaderFelder / $anzahlFelder)");
          //logDebug (print_r ($fields, true), "");
          //logDebug (print_r ($headerFields, true), "");
          die ();
        }
        else
        {
          try
          {
            $db->query ($sql);
          } catch (Zend_Exception $e)
          {
            logError ($sql, "");
          }
        }
      }
      fclose ($fileHandle);
    }


    /**
     *
     * erstellt eine neue Import-Tabelle
     *
     * @return int
     */
    public function createImportTable ()
    {
      // 1. Zeile aus dem CSV auslesen und die Spaltennamen als Spaltennamen in der Tabelle nehmen. Diese neu anlegen.
      $db = Zend_Registry::get ('db');
      try
      {
        $fileHandle = fopen ($this->csvPath . $this->csvFileName, "r");
        try
        {
          $firstLine = $this->readln ($fileHandle, 240);
          fclose ($fileHandle);
          // Feldnamen für die Verwendungen in der DB aufbereiten (Leerzeichen raus, Umlaute uebersetzen)
          $firstLine = strtolower ($firstLine);
          $firstLine = str_replace (' ', '_', $firstLine);
          $firstLine = str_replace ('-', '_', $firstLine);
          $firstLine = str_replace (chr (228), 'ae', $firstLine);
          $firstLine = str_replace (chr (246), 'oe', $firstLine);
          $firstLine = str_replace (chr (252), 'ue', $firstLine);
          $fields = explode ('|', $firstLine);
          $anzahlFelder = count ($fields);
          // da sich die Felder ändern können, wird die Tabelle jedesmal gelöscht bevor neu importiert wird.
          // TODO es muss dann darauf geachtet werden, dass immer die richtigen Felder abgefragt werden beim Datenimport!!!
          $sql = "DROP TABLE vm_import_kunden";
          $db->query ($sql);
          $sql = "CREATE TABLE vm_import_kunden (";
          foreach ($fields as $key => $fieldName)
          {
            $sql .= $fieldName . " VARCHAR(128)";
            if (($key + 1) < $anzahlFelder)
            {
              $sql .= ",";
            }
          }
          $sql .= ")";
          $db->query ($sql);
          $this->importData ();
        } catch (Zend_Exception $e)
        {
          logError ($e->getMessage (), "AnbieterData::createNewImportTable");
        }
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "AnbieterData::createNewImportTable");
        return 2; // Return-Code ==> Fehler beim Speichern
      }
    }
  }

?>
