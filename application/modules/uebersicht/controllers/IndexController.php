<?php
  /**
   * Modul Übersicht
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Uebersicht_IndexController extends Zend_Controller_Action
  {


    /**
     * setzt alle Grunddaten für die Übersicht im View
     *
     * @return void
     */
    public function indexAction ()
    {
      $layout = Zend_Layout::getMvcInstance ();
////logDebug (print_r ($layout, true), "");
      $this->view->anbieterDetails = $layout->anbieterDetails;
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
      $this->view->premiumLevel = $anbieterDetails ['PREMIUMLEVEL'];
      $startDatum = $anbieterDetails ['STARTDATUM'];
      $laufzeit = $anbieterDetails ['LAUFZEIT'];
      $startdatumArray = explode ('-', $startDatum);
      $endDatum_ts = mktime (0, 0, 0, $startdatumArray [1] + $laufzeit, $startdatumArray [2], $startdatumArray [0]);
      $startDatum_ts = mktime (0, 0, 0, $startdatumArray [1], $startdatumArray [2], $startdatumArray [0]);
      $now_ts = mktime (0, 0, 0, date ('m'), date ('d'), date ('Y'));
      $endDatum = date ('d.m.Y', $endDatum_ts);
      $startDatum = date ('d.m.Y', $startDatum_ts);
      $nowDatum = date ('d.m.Y', $now_ts);
      $restlaufzeit = round (($endDatum_ts - $now_ts) / (60 * 60 * 24));
      $this->view->restlaufzeit = $restlaufzeit;
      $this->view->startdatum = $startDatum;
      $this->view->enddatum = $endDatum;
      $this->view->lastLogin = $anbieterDetails ['LAST_LOGIN'];
    }
  }

?>