<?php

/**
 * Datenbank-Model für allgemeine Daten
 *
 * @author Thomas Grahammer
 * @version $id$
 *
 */
class Model_DbTable_GeneralData extends Zend_Db_Table_Abstract
{

  /**
   * @deprecated function
   *
   * @param $searchPhrase
   * @return mixed
   */

  public function searchAnbieter ($searchPhrase)
  {
    $db = Zend_Registry::get ('db');
    $select = $db->select();
    $select->from (array ('u' => 'user'))
           ->join (array ('ud' => 'userDetails'), 'u.userDetailID = ud.userDetail_ID')
           ->join (array ('land' => 'laender'), 'ud.landID = land.laenderID')
           ->where ('ud.firmenname like "'.$searchPhrase.'%"', $searchPhrase);
//           ->orWhere ('ud.nachname like "'.$searchPhrase.'%"', $searchPhrase)
//           ->orWhere ('ud.vorname like "'.$searchPhrase.'%"');

    $result = $select->query ();
    $data = $result->fetchAll ();
    array_walk_recursive ($data, 'utfDecode');
    foreach ($data as $key => $hit)
    {
      $anbieter ['hits'] [$key] = $hit;
    }

    return $anbieter;
  }

  /**
   * @depricated function
   *
   * @param $userID
   * @return array
   */
  public function getAnbieterDetails ($userID)
  {
    $db = Zend_Registry::get ('db');
    $select = $db->select();
    $select->from (array ('u' => 'user'))
           ->join (array ('ud' => 'userDetails'), 'u.userDetailID = ud.userDetail_ID')
           ->join (array ('sd' => 'stammdaten'), 'sd.userID = u.userID')
           ->join (array ('land' => 'laender'), 'ud.landID = land.laenderID')
           ->where ('u.userID = ?', $userID);

    $result = $select->query ();
    $data = $result->fetch ();
    array_walk_recursive ($data, 'utfDecode');
    foreach ($data as $key => $value)
    {
      $key = strtoupper ($key);
      $anbieter [$key] = $value;
    }
////logDebug (print_r ($anbieter, true), "tgr");
    return $anbieter;
  }





}

?>
