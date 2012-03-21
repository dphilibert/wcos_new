<?php

  /**
   * Ajax-Handler für das Modul Übersicht
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Uebersicht_UebersichtAjaxController extends Zend_Controller_Action
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
      $sessionUserHash = $sessionNamespace->userData->userHash;
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
     * schaltet den Prmiumlevel des Benutzers um
     *
     * @return void
     */
    public function switchpremiumlevelAction ()
    {
      $aID = $this->getRequest ()->getParam ('anbieterID');
      $model = new Model_DbTable_AnbieterData ();
      $anbieterDetails = $model->getAnbieterDetails ($aID);
      $oldStatus = $anbieterDetails ['PREMIUMLEVEL'];
      if ($oldStatus == 0) {
        $newStatus = 1;
      }
      if ($oldStatus == 1) {
        $newStatus = 0;
      }
      $model->saveAnbieter ('premiumLevel', $newStatus, $aID);
      $response = array('newPremiumLevel' => $newStatus);
      $this->_helper->json->sendJson ($response);
    }
  }

?>
