<?php
  /**
   * Datenbank-Model für Stammdaten
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Model_DbTable_StammdatenData extends Zend_Db_Table_Abstract
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
     * liefert die Stammdaten eines Anbieters
     *
     * @param int $ID anbieterID
     *
     * @return mixed
     */
    public function getStammdaten ($ID)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('a' => 'anbieter'))
      ->join (array('s' => 'stammdaten'),
        'a.stammdatenID = s.stammdatenID');
      $select->where ("a.anbieterID = ?", $ID);
      $result = $select->query ();
      $data = $result->fetchAll ();
      ////logDebug (print_r ($data, true), "getStammdaten");
      return $data;
    }


    /**
     * speichert die Stammdaten eines Anbieters
     *
     * @param string $field DB-Feld
     * @param string $value DB-Wert
     * @param $stammdatenID stammdatenID
     *
     * @return int 0=erfolgreich, 2=Fehler beim speichern
     */
    public function saveStammdaten ($field = NULL, $value, $stammdatenID)
    {
      $db = Zend_Registry::get ('db');
      $tableName = "stammdaten";
      $select = $db->select ();
      $select->from (array('s' => 'stammdaten'));
      $select->where ("s.stammdatenID = ?", $stammdatenID);
      $result = $select->query ();
      $data = $result->fetchAll ();
      if (!count ($data) > 0) // Stammdaten gibt es noch nicht => neu anlegen
      {
        try
        {
          $db->query ("INSERT INTO stammdaten () VALUES ()"); // leeren Datensatz anlegen
        } catch (Exception $e)
        {
          logError ($e->getMessage (), "StammdatenAjaxController::saveStammdaten INSERT");
        }
        $ID = $db->lastInsertId ();
      }
      $whereCond = "stammdatenID = $stammdatenID";
      $data = array($field => $value);
      try
      {
        $n = $db->update ($tableName, $data, $whereCond);
        //dataChangeMail("Stammdaten");
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "StammdatenAjaxController::saveStammham UPDATE");
        return 2; // Return-Code ==> Fehler beim Speichern
      }
      return 0; // Return-Code ==> Speichern erfolgreich
    }
  }

?>
