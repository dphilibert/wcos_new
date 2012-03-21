<?php

  /**
   * Datenbank-Model für die Ansprechpartner
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Model_DbTable_AnsprechpartnerData extends Zend_Db_Table_Abstract
  {

    var $vorname = NULL;

    /**
     *
     * initiale init-Funktion
     *
     * @return void
     *
     */
    public function init ()
    {
    }

    /**
     *
     * liefert eine Liste mit Ansprechpartnern zu einem Anbieter
     *
     * @param string $token Suchbegriff (token)
     * @param $anbieterID anbieterID
     * @param bool $str2upper String umwandeln in Grossbuchstaben?
     *
     * @return mixed
     */
    public function getAnsprechpartnerList ($token = NULL, $anbieterID = NULL, $str2upper = FALSE)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('a' => 'ansprechpartner'))
      ->joinLeft (array('m' => 'media'),
        'm.mediaID = a.mediaID');
      //    if ($token != NULL && $token != 'undefined') $select->where ("a.nachname like '%$token%'");
      //if ($token != NULL && $token != 'undefined') $select->where ("CONCAT(a.vorname, '', a.nachname) LIKE '%$token%'");
      if ($token != NULL && $token != 'undefined' && $token != '') {
        $select->where ("a.vorname LIKE '$token%' OR a.nachname LIKE '$token%'");
      }
      $select->where ("a.status >= 0 AND a.anbieterID = ?", $anbieterID);
      $result = $select->query ();
      $data = $result->fetchAll ();
      return $data;
    }


    /**
     * liefert einen Ansprechpartner-Datensatz zu einer ansprechpartnerID
     *
     * @param int $apID ansprechpartnerID
     *
     * @return mixed
     *
     */
    public function getAnsprechpartner ($apID = NULL)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('ap' => 'ansprechpartner'));
      $select->where ('ap.ansprechpartnerID = ?', $apID);
      $result = $select->query ();
      $fooData = $result->fetchAll ();
      $fetchData = $fooData;
      return $fetchData;
    }

    /**
     * speichert den Ansprechpartner in die DB
     *
     * @param string $field DB-Feld
     * @param string $value DB-Value
     * @param $apID ansprechpartnerID
     *
     * @return int 0=erfolgreich, 2=Fehler beim speichern
     *
     */
    public function saveAnsprechpartner ($field = NULL, $value, $apID)
    {
      $db = Zend_Registry::get ('db');
      $db->getProfiler ()->setEnabled (true);
      if ($field != NULL) // nur ein Feld des Users speichern
      {
        $tableName = "ansprechpartner";
        $whereCond = "ansprechpartnerID = $apID";
      }
      $data = array($field => $value);
      $data ['status'] = 1;
      try
      {
        $n = $db->update ($tableName, $data, $whereCond);
        //dataChangeMail("Stammdaten", print_r ($data, true));
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "AnsprechpartnerAjaxController::saveAnsprechpartner");
        $profiler = $db->getProfiler ();
        $foo = $profiler->getLastQueryProfile ();
        logError (print_r ($foo, true), "");
        return 2; // Return-Code ==> Fehler beim Speichern
      }
      return 0; // Return-Code ==> Speichern erfolgreich
    }


    /**
     * direktes löschen des Ansprechpartners
     *
     * @depricated
     *
     * @param int $apID ansprechpartnerID
     *
     */
    public function hardDelAnsprechpartner ($apID)
    {
      $db = Zend_Registry::get ('db');
      try
      {
        $db->query ("DELETE FROM ansprechpartner WHERE ansprechpartnerID=$apID");
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "AnsprechpartnerAjaxController::hardDelAnsprechpartner");
      }
    }


    /**
     * legt einen neuen, leeren Ansprechpartner-Datensatz an
     *
     * @param int $anbieterID anbieterID
     *
     * @return int ID des eingefügten Datensatzes
     *
     */
    public function newAnsprechpartner ($anbieterID)
    {
      $db = Zend_Registry::get ('db');
      try
      {
        $db->query ("INSERT INTO ansprechpartner (anbieterID, status) VALUES ($anbieterID, 0)");
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "AnsprechpartnerAjaxController::newAnsprechpartner");
      }
      return $db->lastInsertId ();
    }
  }

