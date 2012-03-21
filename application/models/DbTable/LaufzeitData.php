<?php
  /**
   * Datenbank-Model Laufzeit
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   *
   */
  class Model_DbTable_LaufzeitData extends Zend_Db_Table_Abstract
  {

    /**
     * setzt die Laufzeit eines Premiumaccounts
     *
     * @param int $anbieterID anbieterID
     * @param int $laufzeit Laufzeit in Monaten
     */
    public function setLaufzeit ($anbieterID, $laufzeit)
    {
      $db = Zend_Registry::get ('db');
      try
      {
        $startdatum = date ('Y-m-d H:m:i');
        $sql = "INSERT INTO laufzeiten (anbieterID, startdatum, laufzeit) values ($anbieterID, '$startdatum', $laufzeit)";
        $db->query ($sql);
      } catch (Zend_Exception $e)
      {
        logError ($e->getMessage (), "Model_DbTable_Laufzeit::setLaufzeit");
      }
    }

  }

?>
