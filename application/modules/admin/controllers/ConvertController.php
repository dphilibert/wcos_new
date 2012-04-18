<?php

  /**
   * Modul Admin - Convert-Handler
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Admin_ConvertController extends Zend_Controller_Action
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

    /**
     * konvertiert die ansprechpartner
     */
    public function ansprechpartnerAction ()
    {
      $model = new Model_DbTable_AnsprechpartnerData();
    }

    /**
     * konvertiert die firmenportraits
     */
    public function firmenportraitsAction ()
    {
    }

    /**
     * konvertiert die jobs
     */
    public function jobsAction ()
    {
    }

    /**
     * konvertiert die Laufzeiten
     */
    public function laufzeitenAction ()
    {
    }

    /**
     * konvertiert die Medien
     */
    public function mediaAction ()
    {
    }

    /**
     * koncvertiert die Messen
     */
    public function messenAction ()
    {
    }

    /**
     * konvertiert die Termine
     */
    public function termineAction ()
    {
    }

    /**
     * konvertiert die Whitepaper
     */
    public function whitepaperAction ()
    {
    }
  }

?>
