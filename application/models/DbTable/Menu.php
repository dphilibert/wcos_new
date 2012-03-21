<?php
  /**
   * Datenbank-Model Menu
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Model_DbTable_Menu extends Zend_Db_Table_Abstract
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
     * liefert einen Menu-Eintrag
     *
     * @param int $parentID die ID des Parents (darüberliegender Eintrag)
     *
     * @return array
     */
    public function getMenu ($parentID)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ()
      ->from ('menu')
      ->where ('parentID = ?', $parentID);
      $result = $select->query ();
      $fooData = $result->fetchAll ();
      foreach ($fooData as $key => $value)
      {
        $data [] = $value;
      }
      return $data;
    }
  }

?>
