<?php

  /**
   * Modul Admin - Verlagsmanager-Handler
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Admin_VerlagsmanagerController extends Zend_Controller_Action
  {

    /**
     * setzte initiale Werte für das View
     *
     * @return void
     */
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

    /**
     * leere Funktion damit beim Aufruf von index keine Exception generiert wird
     *
     * @return void
     */
    public function indexAction ()
    {
    }
    
    public function importAction ()
    {
      
    }        
  }

?>
