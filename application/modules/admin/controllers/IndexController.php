<?php

  /**
   * Modul Admin - Index
   *
   * @author Thomas Grahammer
   * @version $id$
   */
  class Admin_IndexController extends Zend_Controller_Action
  {

    /**
     * setzt initial-Werte für das View
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
      try
      {
        $anbieterModel = new Model_DbTable_AnbieterData ();
        $anbieterDetails = $anbieterModel->getAnbieterDetails ($anbieterID);
        $this->view->anbieterHash = $anbieterDetails ['ANBIETERHASH'];
      }
      catch (Zend_Exception $e)
      {
        $redirect = new Zend_Controller_Action_Helper_Redirector();
        $redirect->gotoUrl ('/login');
      }
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
