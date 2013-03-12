<?php

  /**
   * Modul Termine - Index
   *
   * @author Thomas Grahammer
   * @version $id$
   */

class Termine_IndexController extends Zend_Controller_Action
{

 /**
  * setzt die Termin-Daten für das View
  *
  * @return void
  */
  public function indexAction ()
  {
//    $ansprechpartnerModel = new Model_DbTable_AnsprechpartnerData ();
//    $this->view->ansprechpartner = $ansprechpartnerModel->getAnsprechpartnerList ();
//    $this->view->anreden = Model_DbTable_Anreden::getAnreden (1);
//    $this->view->data = $ansprechpartnerModel->getAnsprechpartner ($apID);

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
    

    $termineModel = new Model_DbTable_TermineData ();

    $sessionNamespace = new Zend_Session_Namespace ();
    $userData = $sessionNamespace->userData;
    $anbieterID = $userData ['anbieterID'];
    $this->view->aID = $anbieterID;

    $this->view->termine = $termineModel->getTermineList ($anbieterID);
  }



}

?>
