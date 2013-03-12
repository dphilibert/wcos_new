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
    /*
    $moduleName = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
    $actionName = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
    $controllerName = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();

    $sessionNamespace = new Zend_Session_Namespace ();
    $userData = $sessionNamespace->userData;
    $userID = $userData ['userID'];
    $anbieterID = $userData ['anbieterID'];
    $ansprechpartnerModel = new Model_DbTable_AnsprechpartnerData ();
    $this->view->anbieterID = $anbieterID;   

    $anbieterModel = new Model_DbTable_AnbieterData ();
    $anbieterDetails = $anbieterModel->getAnbieterDetails ($anbieterID);
    $this->view->anbieterHash = $anbieterDetails ['ANBIETERHASH'];

    $db = Zend_Registry::get ('db');

    $sql = "INSERT INTO history (userID, anbieterID, module, controller, action) 
                         VALUES ($userID, $anbieterID, '$moduleName', '$actionName', '$controllerName')";

    // $db->query ($sql);

    ////logDebug ($userID."|".$anbieterID."|".$moduleName."|".$actionName."|".$controllerName, "tgr");
*/
  }
}


?>
