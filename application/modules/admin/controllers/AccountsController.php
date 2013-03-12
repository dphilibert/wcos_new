<?php

  /**
   * Modul Admin - Accounts
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Admin_AccountsController extends Zend_Controller_Action
  {

    /**
     * setzt die initialen Werte für das View
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


    /**
     * leere Funktion damit beim Aufruf von index keine Exception generiert wird
     *
     * @return void
     */
    public function indexAction ()
    {
    }
  }

?>
