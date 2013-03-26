<?php

class Admin_StatistikenController extends Zend_Controller_Action
{

   public function init ()
   {  
     $sessionNamespace = new Zend_Session_Namespace ();
     $userData = $sessionNamespace->userData;
     $anbieterID = $userData ['anbieterID'];
     $ansprechpartnerModel = new Model_DbTable_AnsprechpartnerData ();
     $this->view->anbieterID = $anbieterID;
     $anbieterModel = new Model_DbTable_AnbieterData ();
     if ($anbieterID > 0)
     {
       $anbieterDetails = $anbieterModel->getAnbieterDetails ($anbieterID);
     }
     else
     {
       $this->_helper->redirector->gotoUrl ('/login/index/index');
     }
     $this->view->anbieterHash = $anbieterDetails ['ANBIETERHASH'];
   }


   public function indexAction ()
   {
     
   }        
   
}


?>