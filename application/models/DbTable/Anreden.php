<?php

  /**
   *
   * Datenbank-Model für die Tabelle anreden
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Model_DbTable_Anreden extends Zend_Db_Table_Abstract
  {

    /**
     *
     * initiale init-Funktion
     *
     */
    public function init ()
    {
    }

    /**
     *
     * holt eine Liste von Anreden für die angegebene Sprache
     *
     * @param int $sprachID sprachID
     *
     * @return mixed
     *
     **/
    public function getAnreden ($sprachID = 1)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ()
      ->from (array('a' => 'anreden'))
      ->where ('a.sprachID = ?', $sprachID);
      $result = $select->query ();
      $data = $result->fetchAll ();
      return $data;
    }
  }

?>
