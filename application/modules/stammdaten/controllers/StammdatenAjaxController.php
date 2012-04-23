<?php

  /**
   * Modul Stammdaten - Ajax-Handler
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   *
   */
  class Stammdaten_StammdatenAjaxController extends Zend_Controller_Action
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
      ////logDebug (print_r ($sessionNamespace, true), "preDispatch");
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
     * Stammdaten speichern
     *
     * @return void
     */
    public function saveAction ()
    {
      //     Model_History::save2history ();
      $ID = $this->getRequest ()->getParam ('id');
      $anbieterID = $this->getRequest ()->getParam ('anbieterID');
      $model = new Model_DbTable_StammdatenData ();
      $anbieterModel = new Model_DbTable_AnbieterData();
      $selectedField = $this->getRequest ()->getParam ('field');
      $dbField = $selectedField;
      $selectedValue = $this->getRequest ()->getParam ('value');
      if ($dbField != 'firmenname')
      {
        $model->saveStammdaten ($dbField, $selectedValue, $ID);
      }
      if ($dbField == 'firmenname')
      {
        $anbieterModel->saveAnbieter ($dbField, $selectedValue, $anbieterID);
      }
    }

    /**
     * Stammdaten laden und als json ausliefern
     *
     * @return void
     */
    public function loadAction ()
    {
      $vmkdnrhx = $this->getRequest ()->getParam ('vmkdnrhx');
      $vmkdnr = base64_decode ($vmkdnrhx);
      logDebug ("firmaKundennummer: " . $vmkdnr, "");
      $ID = $this->getRequest ()->getParam ('id');
      $model = new Model_DbTable_StammdatenData ();
      $rawData = $model->getStammdaten ($ID);
      // hier VM-Anbindung rein und als Fallback das o.a. local-Model nehmen
      $response = $rawData [0];
      $this->_helper->json->sendJson ($response);
    }

    /**
     * email versenden, dass Stammdaten geändert wurden
     *
     * @return void
     */
    public function sendchangemailAction ()
    {
      $anbieterID = $this->getRequest ()->getParam ('anbieterID');
      $stammdatenModel = new Model_DbTable_StammdatenData ();
      $stammdaten = $stammdatenModel->getStammdaten ($anbieterID);
      $stammdaten = $stammdaten [0];
      $tempdata = $this->getRequest ()->getParam ('tempdata');
      $differences = $this->compareData ($tempdata);
      // dataChangeMail ($anbieterID, "Stammdaten", print_r ($stammdaten, true));
    }

    /**
     * Premium-Account-Informationen per Mail versenden
     *
     * @return void
     */
    public function requestpremiuminfoAction ()
    {
      $config = Zend_Registry::get ('config');
      $anbieterID = $this->getRequest ()->getParam ('anbieterID');
      $anbieterModel = new Model_DbTable_AnbieterData();
      $anbieterData = $anbieterModel->getAnbieter ($anbieterID);
      $stammdatenModel = new Model_DbTable_StammdatenData ();
      $stammdaten = $stammdatenModel->getStammdaten ($anbieterID);
      $stammdaten = $stammdaten [0];
      $premiumHash = md5 ($anbieterData ['anbieterhash']);
      $premiumLink = 'http://' . $_SERVER ['SERVER_NAME'] . '/stammdaten/index/makeitpremium/hash/' . $premiumHash;
      $mail = new Zend_Mail ();
      $mailHtml = 'Der Anbieter "' . $anbieterData ['firmenname'] . '" wünscht Informationen über einen Premium-Account.<br><br>';
      ////logDebug (print_r ($anbieterData, true), "");
      $mailHtml .= "Firmenname: " . $anbieterData ['firmenname'] . "<br>";
      $mailHtml .= "Anschrift: " . $stammdaten ['strasse'] . " " . $stammdaten ['hausnummer'] . "<br>";
      $mailHtml .= "Plz/Ort: " . $stammdaten ['plz'] . " " . $stammdaten ['ort'] . "<br>";
      $mailHtml .= "Telefon: " . $stammdaten ['fon'] . "<br>";
      $mailHtml .= "Telefax: " . $stammdaten ['fax'] . "<br>";
      $mailHtml .= "email: " . $stammdaten ['email'] . "<br><br><br>";
      $mailHtml .= '<a href="' . $premiumLink . '" target="_new">Premium-Account aktivieren</a><br><br><br><br><br>';
      $mailHtml .= "<small>Im Falle eines Fehlers bitte folgende Daten an die Technik weiterleiten:<br>";
      $mailHtml .= print_r ($anbieterData, true);
      $mailHtml = utf8_decode ($mailHtml);
      //$mail->setBodyText ($mailText);
      $mail->setBodyHtml ($mailHtml);
      $mail->setFrom ($config->mail->from->address, $config->mail->from->text);
      $mail->addTo ($config->mail->to->address);
      $mail->setSubject ('WCOS Systemnachricht: PremiumAccount-Anfrage');
      $mail->send ();
    }

    /**
     * Premium-Account-Informationen laden und als json ausliefern
     *
     * @return void
     */
    public function loadpremiuminfoformAction ()
    {
      $view = $this->view; // damit sind die View-Eigenschaften aus den Sub-Aufrufen (Sub-Methoden) auch im View verfuegbar
      $viewPath_fs = APPLICATION_PATH . '/modules/stammdaten/views/scripts/ajax/';
      $viewFile = 'loadpremiuminfoform.phtml';
      if (file_exists ($viewPath_fs . $viewFile))
      {
        $view->addScriptPath ($viewPath_fs);
        $response ['html'] = $view->render ($viewFile);
      }
      else
      {
        $response ['html'] = "file not found";
      }
      $this->_helper->json->sendJson ($response);
    }


    /**
     * prüft die Systeme zu einem Anbieter
     *
     * @return void
     */
    public function checksystemAction ()
    {
      $anbieterID = $this->getRequest ()->getParam ('anbieterID');
      $system2check = $this->getRequest ()->getParam ('system');
      $model = new Model_DbTable_AnbieterData();
      $anbieter = $model->getAnbieter ($anbieterID);
      $systems = $anbieter ['systems'];

      $systemsArray = explode (',', $systems);
      //logDebug (print_r ($systemsArray, true), "tgr2");
      $response = 'false';
      if (in_array ($system2check, $systemsArray))
      {
        $response = 'true';
      }
      $this->_helper->json->sendJson ($response);
    }

    public function switchsystemAction ()
    {
      logDebug ("switching", "");
      $anbieterID = $this->getRequest ()->getParam ('anbieterID');
      $system2check = $this->getRequest ()->getParam ('system');
      $model = new Model_DbTable_AnbieterData();
      $anbieter = $model->getAnbieter ($anbieterID);
      //logDebug (print_r ($system2check, true), "tgr2");
      $systems = $anbieter ['systems'];
      $systemsArray = explode (',', $systems);
      $response = 'false';
      // prüfen ob system in systems enthalten
      // wenn ja, entfernen, ansonsten hinzufügen
      if (in_array ($system2check, $systemsArray))
      {
        $sKey = array_search ($system2check, $systemsArray);
        unset ($systemsArray [$sKey]);
      }
      else
      {
        $systemsArray [] = $system2check;
      }
      // $systemsArray in String zurück und zurückschreiben

      if (count ($systemsArray) > 1)
      {
        $systems = implode (",", $systemsArray);
      } else
      {
        $systems = $systemsArray;
      }
        $model->saveAnbieter('systems', $systems, $anbieterID);
      $this->_helper->json->sendJson ($response);
    }
  }

?>
