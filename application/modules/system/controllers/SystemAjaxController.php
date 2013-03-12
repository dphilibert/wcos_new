<?php

  /**
   * Systemweiter Ajax-Handler
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class System_SystemAjaxController extends Zend_Controller_Action
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
     * liefert die Options für die Anzahl der Einträge pro seite per json aus
     *
     * @return void
     */
    public function getpagesoptionsAction ()
    {
      /*
           $webseitenID = $this->getRequest ()->getParam ('webseitenID');
           $warenkorbModel = new Model_DbTable_WarenkorbData ();
           $pagedData = $warenkorbModel->getPakete ($webseitenID, $warenkorbID);
           $i = 0;
           foreach ($pagedData as $key => $value)
           {
             $i++;
             $response->rows ['row'.$i] = array ('paketID'             => $value ['paketID'],
                                                 'position'            => $value ['position'],
                                                 'webseitenID'         => $value ['webseitenID']);
           }
      */
// TODO Optionen aus der Session auslesen
      $response ['anzahlEintraegeOptions'] ['10'] = '10';
      $response ['anzahlEintraegeOptions'] ['25'] = '25';
      $response ['anzahlEintraegeOptions'] ['50'] = '50';
      $response ['anzahlEintraegeOptions'] ['100'] = '100';
      $this->_helper->json->sendJson ($response);
    }


    /**
     * liefert die Options für die System-Auswahl per json aus
     *
     * @return void
     */
    public function getoptionssystemeAction ()
    {
      $response ['optionsSysteme'] ['1'] = 'elektroniknet.de';
      $response ['optionsSysteme'] ['2'] = 'computer-automation.de';
      $this->_helper->json->sendJson ($response);
    }
  }

?>
