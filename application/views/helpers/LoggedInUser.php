<?php

class Zend_View_Helper_LoggedInUser 
{

  protected $view;
 
  public function setView ()
  {
    $this->view = $view;
  }

  public function loggedInUser ()
  {
    $auth = Zend_Auth::getInstance ();
    if ($auth->hasIdentity ()) // User hat eine Identitaet, ist also eingeloggt
    {
      $data = $auth->getIdentity ();
      $userID = $data->userID;
      $userDataModel = new Model_DbTable_UserData ();
      $userData = $userDataModel->getUserData ($userID);
      $loggedInUser = $userData ['vorname']." ".$userData ['nachname']." [".$userData ['username']."]";
      return $loggedInUser;
    } 
  }
}

?>
