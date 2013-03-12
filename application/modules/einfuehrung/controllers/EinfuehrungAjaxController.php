<?php

  /**
   * Modul Einführung
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Einfuehrung_StammdatenAjaxController extends Zend_Controller_Action
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
      $model = new Model_DbTable_StammdatenData ();
      $selectedField = $this->getRequest ()->getParam ('field');
      $dbField = $selectedField;
      $selectedValue = $this->getRequest ()->getParam ('value');
////logDebug ("$dbField / $selectedValue", "tgr");
      $model->saveStammdaten ($dbField, $selectedValue, $ID);
    }

    /**
     * Stammdaten laden
     *
     * @return void
     */
    public function loadAction ()
    {
      $ID = $this->getRequest ()->getParam ('id');
      $model = new Model_DbTable_StammdatenData ();
      $rawData = $model->getStammdaten ($ID);
      $response = $rawData [0];
      $this->_helper->json->sendJson ($response);
    }


    /**
     * Premium-Account anfragen
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
  }

?>
