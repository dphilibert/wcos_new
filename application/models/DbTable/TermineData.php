<?php

  /**
   * Datenbank-Model für Termine
   *
   * @author Thomas Grahammer
   * @version $id$
   */
  class Model_DbTable_TermineData extends Zend_Db_Table_Abstract
  {
    /**
     *
     * ininitales Init
     *
     * @return void
     *
     */
    public function init ()
    {
    }

    /**
     * liefert Liste mit Terminen
     *
     * @param int $anbieterID anbieterID
     * @param string $token suchtext
     *
     * @return mixed
     */
    public function getTermineList ($anbieterID, $token = NULL)
    {
      try
      {
        $db = Zend_Registry::get ('db');
        $select = $db->select ();
        $select->from (array('t' => 'termine'))
        ->join (array('tt' => 'terminTypen'),
          'tt.terminTypID = t.typID');
        if ($token != NULL && $token != 'undefined' && $token != '') {
          $select->where ("t.beginn LIKE '$token%' OR t.ende LIKE '$token%' OR t.ort LIKE '$token%'");
        }
        $select->where ("t.status >= 0");
        $select->where ("t.anbieterID = ?", $anbieterID);
        $select->order (array("t.beginn ASC", "t.ende ASC", "t.ort ASC"));
        $result = $select->query ();
        $data = $result->fetchAll ();
      } catch (Zend_Exception $e)
      {
        //logDebug ($e->getMessage (), "Exception:Model_DbTable_TermineData:getTermineList");
      }
      return $data;
    }

    /**
     * liefert eine Liste mit Typen von Terminen
     *
     * @return mixed
     */
    public function getTerminTypenList ()
    {
      try
      {
        $db = Zend_Registry::get ('db');
        $select = $db->select ();
        $select->from (array('tt' => 'terminTypen'));
        $result = $select->query ();
        $data = $result->fetchAll ();
      } catch (Zend_Exception $e)
      {
        //logDebug ($e->getMessage (), "Exception:Model_DbTable_TermineData:getTermineList");
      }
      return $data;
    }


    /**
     * liefert einen Termin
     *
     * @param int $tID terminID
     *
     * @return mixed
     */
    public function getTermin ($tID)
    {
      try
      {
        $db = Zend_Registry::get ('db');
        $select = $db->select ();
        $select->from (array('t' => 'termine'));
        $select->where ("t.termineID = ?", $tID);
        $result = $select->query ();
        $data = $result->fetchAll ();
      } catch (Zend_Exception $e)
      {
        //logDebug ($e->getMessage (), "Exception:Model_DbTable_TermineData:getTermin");
      }
      return $data;
    }


    /**
     * speichert einen Termin-Eintrag
     *
     * @param string $field DB-Feld
     * @param string $value DB-Wert
     * @param int $tID terminID
     *
     * @return int 0=erfolgreich, 2=Fehler beim speichern
     */
    public function saveTermin ($field = NULL, $value, $tID)
    {
      $db = Zend_Registry::get ('db');
      $db->getProfiler ()->setEnabled (true);
      if ($field != NULL) // nur ein Feld des Users speichern
      {
        $tableName = "termine";
        $whereCond = "termineID = $tID";
      }
      $data = array($field => $value);
      $data ['status'] = 1;
      ////logDebug ($tableName." | ".$whereCond." | ".print_r ($data, true), "saveTermin");
      try
      {
        $n = $db->update ($tableName, $data, $whereCond);
      } catch (Exception $e)
      {
        $profiler = $db->getProfiler ();
        $foo = $profiler->getLastQueryProfile ();
        logError ($e->getMessage (), "TermineModel::saveTermin");
        logError (print_r ($foo, true), "");
        return 2; // Return-Code ==> Fehler beim Speichern
      }
      return 0; // Return-Code ==> Speichern erfolgreich
    }


    /**
     * legt einen neuen, leeren Termin-Datensatz an
     *
     * @param int $anbieterID anbieterID
     *
     * @return int ID des eingefügten Datensatzes
     *
     */
    public function newTermin ($anbieterID)
    {
      //logDebug ('new Termin', "ajax:newTermin");
      $db = Zend_Registry::get ('db');
      try
      {
        $db->query ("INSERT INTO termine (anbieterID, status) VALUES ($anbieterID, 0)");
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "TermineModel::newTermin");
      }
      return $db->lastInsertId ();
    }

    /**
     * direktes löschen des Termins
     *
     * @param int $ID terminID
     */
    public function hardDelTermin ($ID)
    {
      //logDebug ('hard del Termin', "tgr");
      $db = Zend_Registry::get ('db');
      try
      {
        $db->query ("DELETE FROM termine WHERE termineID = ?", $ID);
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "TermineModel::hardDelTermin");
      }
    }


    /**
     * direktes löschen abgelaufener Termine
     *
     * @return void
     */
    public function delDepartedTermine ()
    {
      $db = Zend_Registry::get ('db');
      try
      {
        $dateNow = date ('d.m.Y');
        $db->query ("DELETE FROM termine WHERE ende < '$dateNow' AND loeschenTimer > 0");
      } catch (Zend_Exception $e)
      {
        logError ($e->getMessage (), "TermineModel::delDepartedTermine");
      }
    }
  }

?>
