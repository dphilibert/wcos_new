<?php
  /**
   * Ajax-Handler für das Modul Termine
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Termine_TermineAjaxController extends Zend_Controller_Action
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
     * speichert einen Termin
     *
     * @return void
     */
    public function saveAction ()
    {
      Model_History::save2history ();
      $tID = $this->getRequest ()->getParam ('tid');
      $model = new Model_DbTable_TermineData ();
      $selectedField = $this->getRequest ()->getParam ('field');
      $dbField = $selectedField;
      $selectedValue = $this->getRequest ()->getParam ('value');
      $model->saveTermin ($dbField, $selectedValue, $tID);
    }

    /**
     * lädt einen Termin und liefert ihn Json-String aus
     *
     * @return void
     */
    public function loadAction ()
    {
      $tID = $this->getRequest ()->getParam ('tid');
      $aID = $this->getRequest ()->getParam ('anbieterID');
      $model = new Model_DbTable_TermineData ();
      $rawData = $model->getTermin ($tID);
      $response = $rawData [0];
      $model = new Model_DbTable_MediaData ();
      $medienData = $model->getAllMedia ($aID);
      $response ['medienData'] = $medienData;
//logDebug (print_r ($response, true), "loadAction");
      $this->_helper->json->sendJson ($response);
    }

    /**
     * lädt eine Liste aller Termine und liefert sie als Json-String aus
     *
     * @return void
     *
     *
     */
    public function loadlistAction ()
    {
      $aID = $this->getRequest ()->getParam ('anbieterID');
      $model = new Model_DbTable_TermineData ();
      $filter = $this->getRequest ()->getParam ('filter');
      if ($filter == '') {
        $filter = NULL;
      }
      $rawData = $model->getTermineList ($aID, $filter);
      $response = $rawData;
////logDebug (print_r ($response, true), "");
      $this->_helper->json->sendJson ($response);
    }


    /**
     * lädt eine Liste von Termin-Typen und liefert sie als Json-String aus
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
     * löscht einen Termin
     *
     * @return void
     */
    public function delAction ()
    {
      Model_History::save2history ();
      $tID = $this->getRequest ()->getParam ('tid');
      $model = new Model_DbTable_TermineData ();
      $rawData = $model->hardDelTermin ($tID);
    }

    /**
     * erstellt einen neuen Termin und liefert die neue ID als Json aus
     *
     * @return void
     */
    public function newAction ()
    {
      Model_History::save2history ();
      $tID = $this->getRequest ()->getParam ('tid');
      $aID = $this->getRequest ()->getParam ('anbieterID');
      $model = new Model_DbTable_TermineData ();
      $new_tID = $model->newTermin ($aID);
      //logDebug ("new_tID => $new_tID", "TrmineAjaxController::newAction");
      $response = array('termineID' => $new_tID);
      $this->_helper->json->sendJson ($response);
    }

    /**
     * löscht alle leeren-Termin-Zombies
     *
     * @return void
     */
    public function cleartableAction ()
    {
      $response = array();
      $this->_helper->json->sendJson ($response);
    }
  }

?>
