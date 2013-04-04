<?php

  /**
   *
   * Datenbank-Model für das Firmenportrait
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   *
   */
  class Model_DbTable_FirmenportraitData extends Zend_Db_Table_Abstract
  {

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
     * liefert einen Portrait-Datensatz für einen Anbieter
     *
     * @param int $ID anbieterID
     * @return mixed
     */
    public function getFirmenportrait ($ID)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('f' => 'firmenportraits'));
      $select->where ("f.anbieterID = ?", $ID);
      $result = $select->query ();
      $data = $result->fetchAll ();
      //array_walk_recursive ($data, 'utfDecode');
      return $data;
    }

/**
 * speichert das Firmenportrait
 *
 * @param string $field DB-Feld
 * @param string $value DB-Wert
 * @param $ID @depricated
 * @param int $anbieterID anbieterID
 * @return int 0=erfolgreich gespeichert, 2=Fehler beim speichern
 */
    public function saveFirmenportrait ($field = NULL, $value, $ID, $anbieterID)
    {
      $db = Zend_Registry::get ('db');
      $tableName = "firmenportraits";
      $select = $db->select ();
      $select->from (array('f' => 'firmenportraits'));
      $select->where ("f.anbieterID = ?", $anbieterID);
      $result = $select->query ();
      $data = $result->fetchAll ();
      if (!count ($data) > 0) // Firmenportrait gibt es noch nicht => neu anlegen
      {
        try
        {
          $db->query ("INSERT INTO firmenportraits (anbieterID) VALUES ($anbieterID)");
        } catch (Exception $e)
        {
          logError ($e->getMessage (), "FirmenportraitsAjaxController::saveFirmenportrait INSERT");
        }
        $ID = $db->lastInsertId ();
      }
      $whereCond = "anbieterID = $anbieterID";
      $data = array($field => $value);
      //    array_walk_recursive ($data, 'utfEncode');
      try
      {
        $n = $db->update ($tableName, $data, $whereCond);
      } catch (Exception $e)
      {
        
        logError ($e->getMessage (), "FirmenportraitAjaxController::saveFirmenportrait UPDATE");
        return 2; // Return-Code ==> Fehler beim Speichern
      }
      return 0; // Return-Code ==> Speichern erfolgreich
    }
  }

?>
