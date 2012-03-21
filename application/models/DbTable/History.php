<?php

  /**
   * Datenbank-Model für History-Funktionen
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */

class Model_DbTable_History extends Zend_Db_Table_Abstract
{

  public function init ()
  {
  }

  /**
   * speichert Eintrag in der History
   *
   */

  public function save2history ()
  {
    //logDebug ('save to history','');
  }

}

?>
