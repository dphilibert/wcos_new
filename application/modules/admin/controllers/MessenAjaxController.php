<?php

  /**
   * Modul Admin - Messen Ajax-Handler
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Admin_MessenAjaxController extends Zend_Controller_Action
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
     * speichert eine Messe
     *
     * @return void
     */
    public function saveAction ()
    {
      Model_History::save2history ();
      $ID = $this->getRequest ()->getParam ('id');
      $model = new Model_DbTable_TermineData ();
      $selectedField = $this->getRequest ()->getParam ('field');
      $dbField = $selectedField;
      $selectedValue = $this->getRequest ()->getParam ('value');
      $model->saveTermin ($dbField, $selectedValue, $ID);
    }

    /**
     * lädt eine Messe und liefert sie als json aus
     *
     * @return void
     */
    public function loadAction ()
    {
      $ID = $this->getRequest ()->getParam ('id');
      $aID = $this->getRequest ()->getParam ('anbieterID');
      $model = new Model_DbTable_MessenData ();
      $rawData = $model->getMesse ($ID);
      $response = $rawData [0];
      $model = new Model_DbTable_MediaData ();
      $medienData = $model->getAllMedia ($aID);
      $response ['medienData'] = $medienData;
//logDebug (print_r ($response, true), "loadAction");
      $this->_helper->json->sendJson ($response);
    }


    /**
     * lädt eine Liste von Messen und liefert sie als json aus
     *
     * @return void
     */
    public function loadlistAction ()
    {
      $aID = $this->getRequest ()->getParam ('anbieterID');
      $model = new Model_DbTable_MessenData ();
      $rawData = $model->getMessenList ($aID);
      $response = $rawData;
//logDebug (print_r ($response, true), "");
      $this->_helper->json->sendJson ($response);
    }


    /**
     * lädt eine Liste von Messetypen und liefert sie als json aus
     *
     * @return void
     */
    public function loadtypenlistAction ()
    {
      $model = new Model_DbTable_TermineData ();
      $rawData = $model->getTerminTypenList ();
      $response = $rawData;
////logDebug (print_r ($response, true), "");
      $this->_helper->json->sendJson ($response);
    }

    /**
     * löscht eine Messe
     *
     * @return void
     */
    public function delAction ()
    {
      Model_History::save2history ();
      $ID = $this->getRequest ()->getParam ('id');
      $model = new Model_DbTable_TermineData ();
      $rawData = $model->hardDelTermin ($ID);
    }

    /**
     * left eine neue Messe an und liefert die neue ID als json aus
     *
     * @return void
     */
    public function newAction ()
    {
      Model_History::save2history ();
      $ID = $this->getRequest ()->getParam ('id');
      $aID = $this->getRequest ()->getParam ('anbieterID');
      $model = new Model_DbTable_TermineData ();
      $new_ID = $model->newTermin ($aID);
      $response = array('messenID' => $new_ID);
      $this->_helper->json->sendJson ($response);
    }

    /**
     * löscht alle Tabellen-Zombie-Einträge
     *
     * @return void
     *
     */
    public function cleartableAction ()
    {
      $response = array();
      $this->_helper->json->sendJson ($response);
    }
  }

?>
