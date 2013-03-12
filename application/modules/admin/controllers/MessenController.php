<?php

class Admin_MessenController extends Zend_Controller_Action
{
   public function __call ($methodName, $args)
   {
     echo "Admin_VerlagsmanagerController::__call ()<br>";
   }

   public function init ()
   {
     $sessionNamespace = new Zend_Session_Namespace ();
     $userData = $sessionNamespace->userData;
     $anbieterID = $userData ['anbieterID'];
     $ansprechpartnerModel = new Model_DbTable_AnsprechpartnerData ();
     $this->view->anbieterID = $anbieterID;

     $anbieterModel = new Model_DbTable_AnbieterData ();
     $anbieterDetails = $anbieterModel->getAnbieterDetails ($anbieterID);
     $this->view->anbieterHash = $anbieterDetails ['ANBIETERHASH'];
   }

   public function indexAction ()
   {
   }

   public function importAction ()
   {
   }

}
?>
