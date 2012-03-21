<?php

  /**
   * Globale Funktionen
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Plugin_Global extends Zend_Controller_Plugin_Abstract
  {

    /**
     * @var object Sessiondaten User
     */
    var $userData = NULL;


    /**
     * holt Modul, und Anbieterdaten und setzt diese in der Session
     *
     * @param Zend_Controller_Request_Abstract $request
     *
     * @return void
     */
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
      $redirect = new Zend_Controller_Action_Helper_Redirector();
      $config = Zend_Registry::get ('config');
      $sessionNamespace = new Zend_Session_Namespace ();
      if (is_array ($this->userData) && array_key_exists ('hash', $this->userData))
      {
        $this->userData = $sessionNamespace->userData;
        $sessionUserHash = $this->userData ['hash'];
        $layout = Zend_Layout::getMvcInstance ();
        $layout->userIsAdmin = ($this->userData ['status'] > 99);
        $this->getAnbieterData (&$layout, $request); // Methode s.u.
        $anbieterLimits = $this->getAnbieterLimitsBySession ();
        if (is_object ($anbieterLimits))
        {
          //  //logDebug (print_r ($anbieterLimits, true), "tom");
          $request = Zend_Registry::get ('request');
          $module = $request->getModuleName ();
          /*
          if ($anbieterLimits->{'canuse_'.$module} == 0) // Anbieter darf aufgerufene Funktion gar nicht verwenden
          {
            // TODO evtl. noch Fehlerausgabe einbauen
            $redirect->gotoUrl ('/stammdaten/index/index');
          }
          */
        }
      }
      //    //logDebug (print_r ($sessionNamespace->userData, true), "");
      $ourParams = $this->getRequest ()->getParams ();
      if (is_array ($ourParams) && array_key_exists ('sato', $ourParams)) // sato = "switch to anbieter" :-)
      {
        $newAnbieterHash = $ourParams ['sato'];
        $anbieterModel = new Model_DbTable_AnbieterData();
        $anbieter = $anbieterModel->getAnbieterByHash ($newAnbieterHash);
        //logDebug (print_r ($anbieter, true), "");
        if (is_array ($anbieter))
        {
          $layout = Zend_Layout::getMvcInstance ();
          $layout->anbieterID = $anbieter ['anbieterID'];
          $layout->anbieter = $anbieter;
          $sessionNamespace->anbieterData = $anbieter;
          //logDebug (print_r ($sessionNamespace, true), "");
        }
      }
    }

    /**
     * liest die Anbieterdaten aus und setzt sie im Layout
     *
     * @param object $layout Layout
     * @param object $request Request-Objekt
     */
    public function getAnbieterData ($layout, $request)
    {
      if (array_key_exists ('anbieterID', $this->userData))
      {
        $anbieterID = $this->userData ['anbieterID'];
        $layout->anbieterID = $anbieterID;
        if ($anbieterID > 0)
        {
          $anbieterModel = new Model_DbTable_AnbieterData ();
          $layout->anbieterDetails = $anbieterModel->getAnbieterDetails ($anbieterID);
          $layout->isPremiumKunde = ($layout->anbieterDetails ['PREMIUMLEVEL'] > 0);
          $layout->isPremiumKunde = TRUE;
          if ($layout->isPremiumKunde)
          {
            $layout->premiumHash = md5 ($layout->anbieterDetails ['ANBIETERHASH']);
          }
          $layout->anbieterListe = $anbieterModel->getAnbieterList ();
        }
        $layout->requestURI = $request->getRequestUri ();
        $layout->can_use_stammdaten = TRUE;
        $layout->can_use_ansprechpartner = TRUE;
        $layout->can_use_termine = TRUE;
        $layout->can_use_media = TRUE;
        $layout->can_use_firmenportrait = TRUE;
      }
    }


    /**
     * liest die Anbieter-Limits aus
     *
     * @return null|object Anbieter-limits
     */
    public function getAnbieterLimitsBySession ()
    {
      $session = new Zend_Session_Namespace ();
      $anbieterID = $session->userData ['anbieterID'];
      $anbieterLimits = NULL;
      if ($anbieterID > 0)
      {
        $model = new Model_DbTable_AnbieterData ();
        $anbieterLimits = $model->getAnbieterLimits ($anbieterID);
      }
      return $anbieterLimits;
    }
  }

?>
