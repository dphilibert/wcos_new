<?php

// Wir bauen ein eigenes ACL-System, da dass fuer unseren Bedarf funktionaler ist als das
// bestehnde von Zend Framework. Insbesondere da ZF kein ACL mit DB-Unterstuetzung hat.

class Model_Acl extends Zend_Acl
{
  public function __construct ()
  {
  }


  public function getAllGruppen ()
  {
    $db = Zend_Registry::get ('db');
    $select = $db->select()
                 ->from('userGruppen');

    $result = $select->query ();
    $fooData = $result->fetchAll ();
    if (count($fooData) > 0)
    {
      foreach ($fooData as $key => $value)
      {
        $data [$value ['gruppenID']] = $value; 
      } 
    }
    return $data;
  }

  public function isAllowed ($userID, $module = NULL, $controller = NULL, $action = NULL)
  {
    $db = Zend_Registry::get ('db');
    $select = $db->select ()
                 ->from (array ('u2r' => 'user2resource'))
                 ->join (array ('r' => 'resourcen'), 'r.resourceID=u2r.resourceID')
                 ->where ('u2r.userID=?', $userID)
                 ->where ('module = ?', $module)
                 ->where ('controller = ?', $controller)
                 ->where ('action = ?', $action);
    $result = $select->query ();
    $fooData = $result->fetchAll ();
    if (count ($fooData) > 0) return true;
    return false;
  }

  
}

?>
