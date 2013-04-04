<?php

  /**
   * Module Whitepaper
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Whitepaper_IndexController extends Zend_Controller_Action
  {

    /**
     * @var object Model
     */
    var $model = NULL;

    /**
     * setzt das Model klassenweit
     *
     * @return void
     */
    public function init ()
    {
      $this->model = new Model_DbTable_WhitepaperData ();
    }

    /**
     * setzt die Whitepaper-Daten für das View
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
      $model = new Model_DbTable_WhitepaperData ();
      $sessionNamespace = new Zend_Session_Namespace ();
      $userData = $sessionNamespace->userData;
      $anbieterID = $userData ['anbieterID'];
      $this->view->aID = $anbieterID;
      $this->view->whitepaper = $model->getWhitepaperList ($anbieterID);
    }

    /**
     * hebt die Preview-Sperre eines Whitepapers auf (Redakteursfunktion)
     *
     * @return void
     */
    public function unlockAction ()
    {
      $this->_helper->_layout->disableLayout ();
      $this->_helper->viewRenderer->setNoRender (true);     
      $unlockHash = $this->getRequest ()->getParam ('hash');
      $this->model->unlockWhitepaper ($unlockHash);
      die ('<center>Der Eintrag wurde freigegeben!</center>');
    }
  }

?>
