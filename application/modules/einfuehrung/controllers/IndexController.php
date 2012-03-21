<?php

  /**
   * Index Aufruf des Moduls Einfuehrung
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Einfuehrung_IndexController extends Zend_Controller_Action
  {

    /**
     * setzt die View-Informationen für die Einführung
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
        logError (print_r ($e->getMessage (), true), "Stammdaten_IndexController");
        $redirect = new Zend_Controller_Action_Helper_Redirector();
        $redirect->gotoUrl ('/login');
      }
      $mediaModel = new Model_DbTable_MediaData ();
      $this->view->medien = $mediaModel->getMedienList ($anbieterID, 'FIRMENLOGO');
      $stammdatenModel = new Model_DbTable_StammdatenData ();
      $this->view->stammdaten = $stammdatenModel->getStammdaten ($anbieterID);
    }


    /**
     * setzt für den Account einen Premium-Status
     *
     * @return void
     */
    public function makeitpremiumAction ()
    {
      $this->_helper->_layout->disableLayout ();
      $this->_helper->viewRenderer->setNoRender (true);
      // check hashcode
      // set premiumStatus = 99
      $paramPremiumHash = $this->getRequest ()->getParam ('hash');
      $anbieterModel = new Model_DbTable_AnbieterData ();
      $anbieterFound = $anbieterModel->getAnbieterByHash ($paramPremiumHash);
      if (count ($anbieterFound) > 0)
      {
        $model = new Model_DbTable_AnbieterData ();
        $model->saveAnbieter ("premiumLevel", "1", $anbieterFound [0]['anbieterID']);
        $model = new Model_DbTable_LaufzeitData ();
        $model->setLaufzeit ($anbieterFound [0]['anbieterID'], 12); // TODO Laufzeit variabel machen
        echo 'Der Premium-Account für den Anbieter "' . $anbieterFound [0]['firmenname'] . '" wurde aktiviert!';
        //logDebug ('make it premium!', "");
      }
    }
  }

?>
