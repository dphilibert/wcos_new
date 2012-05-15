<?php
  /**
   * Datenbank-Model für Produktcodes
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Model_DbTable_ProduktcodesData extends Zend_Db_Table_Abstract
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
     * liefert die Produktcodes eines Anbieters
     *
     * @param int $ID anbieterID
     *
     * @return mixed
     */
    public function getProduktcodes ($anbieterID)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('pc2kd' => 'vm_produktcode2kdnummer'))
      ->join (array('pc' => 'vm_produktcodes'), 'pc.branchenname_nummer = pc2kd.produktcode');
      $select->where ("pc2kd.vmKundennummer = ?", $anbieterID);
      $result = $select->query ();
      $data = $result->fetchAll ();
      ////logDebug (print_r ($data, true), "getStammdaten");
      return $data;
    }

    /**
     * liefert das Produktspektrum zu einem System und ggf. zu einer Kundennummer
     *
     * @param $systemID
     *
     * @return mixed
     */
    public function getProduktSpektrum ($systemID = 0, $vmKundennummer = NULL)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('pc2kd' => 'vm_produktcode2kdnummer'))
      ->join (array('pc' => 'vm_produktcodes'), 'pc.branchenname_nummer = pc2kd.produktcode');
      if ($systemID > 0)
      {
        if ($vmKundennummer != NULL) $select->where ("pc2kd.systems like '%$systemID%'");
        $select->where ("pc.systems like '%$systemID%'");
      }
      if ($vmKundennummer != NULL) {
        $select->where ("pc2kd.vmKundennummer = ?", $vmKundennummer);
      }
      $select->order ('pc.hauptbegriff');
      $select->order ('pc.oberbegriff');
      $select->order ('pc.branchenname');
      $result = $select->query ();
      $data = $result->fetchAll ();
      ////logDebug (print_r ($data, true), "getStammdaten");
      return $data;
    }


    /**
     * liefert die Anzahl der Firmen zu einem Produktcode
     *
     * @param $systemID
     * @param $produktcodeID
     *
     * @return mixed
     */
    public function countFirmen4Produktcode ($systemID, $produktcodeID)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('pc2kd' => 'vm_produktcode2kdnummer'), array("count(*) as anzahl"))
      ->where ("pc2kd.produktcode = ?", $produktcodeID)
      ->where ("pc2kd.systems like '%$systemID%'");
      $result = $select->query ();
      $data = $result->fetch ();
      //logDebug (print_r ($select->__toString (), true), "getFirmen4Produktcode");
      //logDebug (print_r ($data, true), "");
      return $data;
    }

    /**
     * liefert die Firmen zu einem Produktcode
     *
     * @param $systemID
     * @param $produktcodeID
     *
     * @return mixed
     */
    public function getFirmen4Produktcode ($systemID, $produktcodeID)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('pc2kd' => 'vm_produktcode2kdnummer'), array("*"))
      ->where ("pc2kd.produktcode = ?", $produktcodeID)
      ->where ("pc2kd.systems like '%$systemID%'");
      $result = $select->query ();
      $data = $result->fetchAll ();
      //logDebug (print_r ($select->__toString (), true), "getFirmen4Produktcode");
      //logDebug (print_r ($data, true), "");
      return $data;
    }

    /**
     * liefert den Namen eines Produktcodes zu einer ProduktcodeID
     *
     * @param $produktcodeID
     *
     * @return mixed
     */
    public function getProduktcodeName ($produktcodeID)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('pc' => 'vm_produktcodes'))
      ->where ("pc.branchenname_nummer = ?", $produktcodeID);
      $result = $select->query ();
      $data = $result->fetch ();
      //logDebug (print_r ($select->__toString (), true), "getFirmen4Produktcode");
      //logDebug (print_r ($data, true), "");
      return $data;
    }
  }

?>