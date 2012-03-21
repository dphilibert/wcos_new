<?php

class Admin_AccountsAjaxController extends Zend_Controller_Action
{

  // preDispatch wird vor dem eigentlichen Dispatching aufgerufen

   public function preDispatch()
   {
     // fuer AJAX Layout und View render abschalten
   $this->_helper->_layout->disableLayout ();
   $this->_helper->viewRenderer->setNoRender (true);
   }


   // im Init werden unterschiedliche Contexte fuer das Ajax-Handling gesetzt
   // TODO naeher beschreiben

   public function init ()
   {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('test', 'xml')
                    ->initContext();
   }


   public function loadlistAction ()
   {
     /*
     $model = new Model_DbTable_AnbieterData ();
     $rawData = $model->getAnbieterList (1);
     $response = $rawData;
    ////logDebug (print_r ($response, true), "");
     $this->_helper->json->sendJson ($response);
     */
     $filter = $this->getRequest ()->getParam ('filter');
     if ($filter == '') $filter = NULL;
     $config = Zend_Registry::get ('config');
     $location_soap_zbvs = $config->soap->zbvsPath;
     $soap_client = new SoapClient(null, array ('location' => $location_soap_zbvs,
                                                'uri' => $location_soap_zbvs));
     $sessionNamespace = new Zend_Session_Namespace ();
     $this->userData = $sessionNamespace->userData;
     $response = $soap_client->list_user ($this->userData ['hash'],10,0);
     // hier werden die kompletten Userdaten (aus ZBVS) in das Response-Array eingebaut
     ////logDebug (print_r ($userListe, true), "user");
     /*
     foreach ($response ['user'] as $key => $user)
     {
       foreach ($userListe as $ul_key => $ul_user)
       {
         if ($ul_user ['user_id'] == $user ['userID'])
         {
           $response ['user'] [$key] = $ul_user;
         }
       }
     }
     */
     ////logDebug (print_r ($response, true), "user");
     $this->_helper->json->sendJson ($response);
   }

   public function cleartableAction ()
   {
     $response = array ();
     $this->_helper->json->sendJson ($response);
   }

   public function loadAction ()
   {
     $ID = $this->getRequest ()->getParam ('id');
     $aID = $this->getRequest ()->getParam ('anbieterID');
     $model = new Model_DbTable_AnbieterData ();
     $rawData = $model->getAnbieter ($ID);
     $response = $rawData;

     $config = Zend_Registry::get ('config');
     $location_soap_zbvs = $config->soap->zbvsPath;
     $soap_client = new SoapClient(null, array ('location' => $location_soap_zbvs,
                                                'uri' => $location_soap_zbvs));
     $sessionNamespace = new Zend_Session_Namespace ();
     $this->userData = $sessionNamespace->userData;
     $userListe = $soap_client->list_user ($this->userData ['hash']);
     // hier werden die kompletten Userdaten (aus ZBVS) in das Response-Array eingebaut
     
     foreach ($response ['user'] as $key => $user)
     {
       foreach ($userListe as $ul_key => $ul_user)
       {
         if ($ul_user ['user_id'] == $user ['userID'])
         {
           $response ['user'] [$key] = $ul_user;
         }
       }
     }
     
     ////logDebug (print_r ($response, true), "tgr");
     $response ['userList'] = $userListe;





     ////logDebug (print_r ($response, true), "tgr");
     $this->_helper->json->sendJson ($response);
   }

   public function loaduserAction ()
   {

     $this->_helper->json->sendJson ($response);
   }

   public function adduserAction ()
   {
     $userID = $this->getRequest ()->getParam ('userID');
     $aID = $this->getRequest ()->getParam ('anbieter2edit');
     $model = new Model_DbTable_UserData ();
     $model->addUser2Anbieter ($userID, $aID);
     $this->_helper->json->sendJson ($response);
   }

   public function deluserAction ()
   {
     $userID = $this->getRequest ()->getParam ('userID');
     $aID = $this->getRequest ()->getParam ('anbieter2editID');
     $model = new Model_DbTable_UserData ();
     $model->delUser2Anbieter ($userID, $aID);
     //logDebug ($userID."/".$aID, "tgr");
     $this->_helper->json->sendJson ($response);
   }


   public function usersearchAction ()
   {
     $term = $this->getRequest ()->getParam ('term');
     //logDebug (print_r ($_REQUEST, true));
     $response = array ("1" => "eins", "2" => "zwei");
     //logDebug ("suche ".$term);
     $this->_helper->json->sendJson ($response);
     
     
   }
   
}


?>