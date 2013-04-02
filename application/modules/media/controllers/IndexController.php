<?php

  /**
   * Modul Media
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Media_IndexController extends Zend_Controller_Action
  {
    /**
     * setzt Daten für das View
     *
     * @return void
     */
    public function indexAction ()
    {
      $sessionNamespace = new Zend_Session_Namespace ();
      $userData = $sessionNamespace->userData;
      $anbieterID = $userData ['anbieterID'];      
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
      $model = new Model_DbTable_MediaData ();
      $this->view->medien = $model->getMedienList ($anbieterID);
      $this->view->mediaTypen = $model->getMediaTypen ();
    }
  }

?>
