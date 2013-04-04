<?php

  /**
   * Ajax-Handler für das Modul Whitepaper
   *
   * @author Thomas Grahammer
   * @version $id$
   */
  class Whitepaper_WhitepaperAjaxController extends Zend_Controller_Action
  {

    /**
     * @var object Model
     */
    var $model = NULL;

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
      $this->model = new Model_DbTable_WhitepaperData ();
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
     * speichert einen Whitepaper-Eintrag ab
     *
     * @return void
     */
    public function saveAction ()
    {
      Model_History::save2history ();
      $tID = $this->getRequest ()->getParam ('id');
      $model = new Model_DbTable_WhitepaperData ();
      $selectedField = $this->getRequest ()->getParam ('field');
      $dbField = $selectedField;
      $selectedValue = $this->getRequest ()->getParam ('value');            
      $model->saveWhitepaper ($dbField, $selectedValue, $tID);
    }

    /**
     * lädt einen Whitepaper-Eintrag und liefert ihn als json aus
     *
     * @return void
     */
    public function loadAction ()
    {
      $ID = $this->getRequest ()->getParam ('id');
      $sessionNamespace = new Zend_Session_Namespace ();
      $anbieterData = $sessionNamespace->anbieterData;
      $aID = $anbieterData ['anbieterID'];
      $model = new Model_DbTable_WhitepaperData ();
      $rawData = $model->getWhitepaper ($ID);
      $response = $rawData [0];
      ////logDebug (print_r ($response, true), "loadAction");
      $this->_helper->json->sendJson ($response);
    }

    /**
     * lädt eine Liste von Whitepaper-Einträgen und liefert sie als json aus
     *
     * @return void
     */
    public function loadlistAction ()
    {
      $sessionNamespace = new Zend_Session_Namespace ();
      $anbieterData = $sessionNamespace->anbieterData;
      $aID = $anbieterData ['anbieterID'];
      $model = new Model_DbTable_WhitepaperData ();
      $rawData = $model->getWhitepaperList ($aID);
      $response = $rawData;
      $this->_helper->json->sendJson ($response);
    }

    /**
     * löscht einen Whitepaper-Eintrag
     *
     * @return void
     */
    public function delAction ()
    {
      Model_History::save2history ();
      $ID = $this->getRequest ()->getParam ('id');
      $model = new Model_DbTable_WhitepaperData ();
      $rawData = $model->hardDelWhitepaper ($ID);
    }

    /**
     * löscht die Zombie-Einträge in der Whitepaper-Tabelle
     *
     * @return void
     */
    public function cleartableAction ()
    {
      $model = new Model_DbTable_WhitepaperData ();
      $model->clearWhitepaperTable ();
    }

    /**
     * erzeugt einen neuen Whitepaper-Eintrag und liefert die neue ID als json aus
     *
     * @return void
     */
    public function newAction ()
    {
      $ID = $this->getRequest ()->getParam ('id');
      $sessionNamespace = new Zend_Session_Namespace ();
      $anbieterData = $sessionNamespace->anbieterData;
      $aID = $anbieterData ['anbieterID'];
      $model = new Model_DbTable_WhitepaperData ();
      $new_ID = $model->newWhitepaper ($aID);
      //logDebug ("newID: $new_ID", "whitepaper");
      $response = array('whitepaperID' => $new_ID);
      $this->_helper->json->sendJson ($response);
    }

    /**
     * sperrt einen Whitepaper-Eintrag zur Redakteursfreigabe
     *
     * @return void
     */
    public function lockentryAction () // alle neuen Einträge werden bis zur Freigabe gesperrt
    {
      //TODO Mail verschicken mit hochgeladener Datei bzw. Link und einem Freigabe-Link it Freigabe-Hash
      //TODO Freigabe-Hash in DB eintragen
      $config = Zend_Registry::get ('config');
      $unlockHash = md5 (uniqid ());
      $sessionNamespace = new Zend_Session_Namespace ();
      $anbieterData = $sessionNamespace->anbieterData;
      $aID = $anbieterData ['anbieterID'];
      $originalFilename = $this->getRequest ()->getParam ('fname');
      $whitepaperID = $this->getRequest ()->getParam ('id');
      
      $whitepaper = $this->model->getWhitepaper ($whitepaperID);
      $wpFilename = $whitepaper [0] ['whitepaper_datei'];
      $uploadPath = getcwd () . '/uploads';
      $mail = new Zend_Mail ();
      $wpFile = file_get_contents ($uploadPath . '/' . $wpFilename);
      $at = $mail->createAttachment ($wpFile);
      $at->filename = $originalFilename;
      $at->type = 'application/x-pdf';
      $at->disposition = Zend_Mime::DISPOSITION_INLINE;
      $unlockLink = 'http://' . $_SERVER ['SERVER_NAME'] . '/whitepaper/index/unlock/hash/' . $unlockHash;
      $mailHtml = 'Freigabe: <a href="' . $unlockLink . '" target="_new">' . $unlockLink . '</a>';
      $this->model->lockWhitepaper ($whitepaperID, $unlockHash);
      

      //$mail->setBodyText ($mailText);
      $mail->setBodyHtml ($mailHtml);
      $mail->setFrom ($config->mail->from->address, $config->mail->from->text);
      $mail->addTo ($config->mail->to->address);
      $mail->setSubject ('WCOS Systemnachricht: Bitte Inhalt des Whitepapers prüfen');
      $mail->send ();
    }
  }

?>
