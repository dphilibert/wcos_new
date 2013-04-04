<?php

  /**
   * Modul Firmenportrait
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Firmenportrait_FirmenportraitAjaxController extends Zend_Controller_Action
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
      $sessionNamespace = new Zend_Session_Namespace ();
      $sessionUserHash = $sessionNamespace->userData ['hash'];
      $paramUserHash = $this->getRequest ()->getParam ('userhash');
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


    public function indexAction ()
    {
    }


    /**
     * Firmenportraitdaten speichern
     *
     * @return void
     *
     */
    public function saveAction ()
    {      
      $anbieterID = $this->getRequest ()->getParam ('anbieterID');
      $ID = $this->getRequest ()->getParam ('id');
      $model = new Model_DbTable_FirmenportraitData ();
      $selectedField = $this->getRequest ()->getParam ('field');
      $selectedValue = $this->getRequest ()->getParam ('value');      
      $model->saveFirmenportrait ($selectedField, $selectedValue, $ID, $anbieterID);
    }


    /**
     * Firmenportraitdaten laden und als json ausliefern
     *
     * @return void
     */
    public function loadAction ()
    {
      $model = new Model_DbTable_FirmenportraitData ();
      $anbieterID = $this->getRequest ()->getParam ('anbieterID');
      $rawData = $model->getFirmenportrait ($anbieterID);
      $response = $rawData [0];
      foreach ($response as $key => $value)
      {
        //$response [$key] = utf8_encode ($value);
      }
      $this->_helper->json->sendJson ($response);
    }

    /**
     * lädt die Liste der Portraiteinstellungen und liefert sie als json aus
     *
     * @return void
     */
    public function loadlistAction ()
    {
      $aID = $this->getRequest ()->getParam ('anbieterID');
      // array (<eintrag> => <Datenbankfeld-Name>)
      $response [0] = array('eintrag' => 'Firmenbeschreibung', 'dbfeld' => 'firmenbeschreibung');
      $response [1] = array('eintrag' => 'Produkte / Linecard', 'dbfeld' => 'produkte');
      $response [2] = array('eintrag' => 'Firmenausrichtung', 'dbfeld' => 'firmenausrichtung');
      $response [3] = array('eintrag' => 'Dienstleistungen', 'dbfeld' => 'dienstleistungen');
      $response [4] = array('eintrag' => 'Präsenz', 'dbfeld' => 'praesenz');
      $response [5] = array('eintrag' => 'Zielmärkte', 'dbfeld' => 'zielmaerkte');
      $response [6] = array('eintrag' => 'Standorte/Lager', 'dbfeld' => 'standorte');
      $response [7] = array('eintrag' => 'Qualitätsmanagement', 'dbfeld' => 'qualitaetsmanagement');
      $response [8] = array('eintrag' => 'Gründungsjahr', 'dbfeld' => 'gruendungsjahr');
      $response [9] = array('eintrag' => 'Mitarbeiter', 'dbfeld' => 'mitarbeiter');
      $response [10] = array('eintrag' => 'Umsatz', 'dbfeld' => 'umsatz');
      $this->_helper->json->sendJson ($response);
    }
  }

?>
