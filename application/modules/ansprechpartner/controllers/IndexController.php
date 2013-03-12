<?php

  /**
   * IndexController Modul Ansprechpartner
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Ansprechpartner_IndexController extends Zend_Controller_Action
  {

    /**
     * setzt die Ansprechpartnerdaten für das Index-View
     *
     * @return void
     */
    public function indexAction ()
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
      $mediaModel = new Model_DbTable_MediaData ();
      $this->view->medien = $mediaModel->getMedienList ($anbieterID, 6);
      $this->view->ansprechpartner = $ansprechpartnerModel->getAnsprechpartnerList (NULL, $anbieterID);
      $this->view->anreden = Model_DbTable_Anreden::getAnreden (1);
//    $this->view->myAnsprechpartner = $ansprechpartnerModel->getAnsprechpartner ($apID);
    }
  }

?>
