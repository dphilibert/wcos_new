<?php

class Model_History 
{

  public function getLastEntry ()
  {
    $sessionNamespace = new Zend_Session_Namespace ();
    $userData = $sessionNamespace->userData;
    $userID = $userData ['userID'];
    $anbieterID = $userData ['anbieterID'];

    $db = Zend_Registry::get ('db');
    $select = $db->select ();
    $select->from (array ('h' => 'history'));
    $select->order (array ('last_change DESC'));
    $select->where ('anbieterID = ?', $anbieterID);
    $result = $select->query ();
    $data = $result->fetchAll ();
    return $data [0];
 
    
  }

 
  public function save2history ()
  {   
  }
}


?>
