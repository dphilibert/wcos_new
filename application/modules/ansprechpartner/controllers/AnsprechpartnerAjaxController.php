<?php

  /**
   * übernimmt die ajax-Anfragen für das Modul Ansprechpartner
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Ansprechpartner_AnsprechpartnerAjaxController extends Zend_Controller_Action
  {


    /**
     * preDispatch wird vor dem eigentlichen Dispatching aufgerufen
     *
     * @return void
     */
    public function preDispatch ()
    {
      // fuer AJAX Layout und View render abschalten
      $this->_helper->_layout->disableLayout ();
      $this->_helper->viewRenderer->setNoRender (true);
      // TODO Authentzifizierung des Users erforderlich. Achtung: Session-Hijacking!!!
      $sessionNamespace = new Zend_Session_Namespace ();
      $sessionUserHash = $sessionNamespace->userData ['hash'];
      $paramUserHash = $this->getRequest ()->getParam ('userhash');
      ////logDebug ("Session UserHash: $sessionUserHash / Param-UserHash: $paramUserHash", "tgr");
    }


    /**
     * setzten verschiedener Kontexte für das ajax-Handling
     *
     * @return void
     */
    public function init ()
    {
      $ajaxContext = $this->_helper->getHelper ('AjaxContext');
      $ajaxContext->addActionContext ('view', 'html')
      ->addActionContext ('form', 'html')
      ->addActionContext ('test', 'xml')
      ->initContext ();
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
     * speichert den Ansprechpartner
     *
     * @return void
     */
    public function saveAction ()
    {
      //logDebug ("Ansprechpartner save", "saveAction");
      Model_History::save2history ();
      $apID = $this->getRequest ()->getParam ('apid');
      $model = new Model_DbTable_AnsprechpartnerData ();
      $selectedField = $this->getRequest ()->getParam ('field');
      $dbField = substr ($selectedField, 3);
      $selectedValue = $this->getRequest ()->getParam ('value');
      if (in_array ($dbField, array('vorname', 'nachname', 'abteilung', 'telefon', 'telefax', 'position', 'email', 'bemerkung', 'mediaID')))
      {
        $model->saveAnsprechpartner ($dbField, $selectedValue, $apID);
      }
    }

    /**
     * lädt einen Ansprechpartner und sendet die Daten als json zurück
     *
     * @return void
     */
    public function loadAction ()
    {
      $apID = $this->getRequest ()->getParam ('apid');
      $model = new Model_DbTable_AnsprechpartnerData ();
      $rawData = $model->getAnsprechpartner ($apID);
      $response = $rawData [0];
      logDebug (print_r ($response, true), "");
      $this->_helper->json->sendJson ($response);
    }

    /**
     * lädt eine Liste von Ansprechpartnern und sendet die Daten als json zurück
     *
     * @return void
     */
    public function loadlistAction ()
    {
      $filter = $this->getRequest ()->getParam ('filter');
      if ($filter == '')
      {
        $filter = NULL;
      }
      $sessionNamespace = new Zend_Session_Namespace ();
      $anbieterData = $sessionNamespace->anbieterData;
      $aID = $anbieterData ['anbieterID'];
      $model = new Model_DbTable_AnsprechpartnerData ();
      $rawData = $model->getAnsprechpartnerList ($filter, $aID);
      ////logDebug (print_r ($rawData, true), "loadAction");
      $response = $rawData;
      $this->_helper->json->sendJson ($response);
    }

    /**
     * löscht einen Ansprechpartner
     *
     * @return void
     */
    public function delAction ()
    {
      Model_History::save2history ();
      $apID = $this->getRequest ()->getParam ('apid');
      $model = new Model_DbTable_AnsprechpartnerData ();
      $rawData = $model->hardDelAnsprechpartner ($apID);
    }

    /**
     * legt einen neuen Ansprechpartner an
     *
     * @return void
     */
    public function newAction ()
    {
      $sessionNamespace = new Zend_Session_Namespace ();
      $anbieterData = $sessionNamespace->anbieterData;
      $anbieterID = $anbieterData ['anbieterID'];
      $apID = $this->getRequest ()->getParam ('apid');
      $model = new Model_DbTable_AnsprechpartnerData ();
      $new_apID = $model->newAnsprechpartner ($anbieterID);
      $response = array('apID' => $new_apID);
      $this->_helper->json->sendJson ($response);
    }
  }

?>
