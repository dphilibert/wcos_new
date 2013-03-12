<?php

  /**
   * Ajax-Handler für das Modul Media
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Media_MediaAjaxController extends Zend_Controller_Action
  {

    /**
     * preDispatch wird vor dem eigentlichen Dispatching aufgerufen
     *
     * @return void
     *
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
     * setzt den Context für Ajax
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
     * lädt ein Medium und liefert die Daten aus json aus
     *
     * @return void
     */
    public function loadAction ()
    {
      $mID = $this->getRequest ()->getParam ('id');
      $model = new Model_DbTable_MediaData ();
      $rawData = $model->getMedia ($mID);
      $response = $rawData [0];
      $this->_helper->json->sendJson ($response);
    }

    /**
     * lädt eine Liste mit Medien-Typen und liefer diese als json aus
     *
     * @return void
     */
    public function loadtypenlistAction ()
    {
      $sessionNamespace = new Zend_Session_Namespace ();
      $anbieterData = $sessionNamespace->anbieterData;
      $anbieterID = $anbieterData ['anbieterID'];
      $model = new Model_DbTable_MediaData ();
      $rawData = $model->getMediaTypen ();
      $response = $rawData;
      ////logDebug (print_r ("doo", true), "tgr");
      $this->_helper->json->sendJson ($response);
    }

    /**
     * lädt eine Liste mit Medien und gibt diese als json zurück
     *
     * @return void
     */
    public function getmedienlisteAction ()
    {
      $sessionNamespace = new Zend_Session_Namespace ();
      $anbieterData = $sessionNamespace->anbieterData;
      $anbieterID = $anbieterData ['anbieterID'];
      $filterArray = $this->getRequest ()->getParam ('filter');
      $model = new Model_DbTable_MediaData ();
      $rawData = $model->getMedienList ($anbieterID, $filterArray);
      $response = $rawData;
      $this->_helper->json->sendJson ($response);
    }

    /* DEPRICATED

       public function uploadOLDAction ()
       {
         Model_History::save2history ();
         $config = Zend_Registry::get ('config');
         $allowedExtensions = $config->uploads->allowedExtensions;
         try
         {
    //TODO Limits aus den persölichen Limits (abhängig von Paketen) des Users setzen
           $adapter = new Zend_File_Transfer_Adapter_Http ();
           $adapter->addValidator ('Count', false, array ('min' => '1', 'max' => '1')) // maximal eine Datei gleichzeitig
    //               ->addValidator ('Size', false, array ('max' => '10MB')) // maximal 100000 Byte grosse Datei
                   ->addValidator ('Extension', false, $allowedExtensions);
           $files = $adapter->getFileInfo ();
           $mediaID = $this->getRequest ()->getParam ('mediaid');
           $mediaTyp = $this->getRequest ()->getParam ('mediatyp');
           $mediaBeschreibung = $this->getRequest ()->getParam ('beschreibung');
    //logDebug ("mediaID = $mediaID | mediaTyp = $mediaTyp | mediaBeschreibung = $mediaBeschreibung", "uploadAction");
    //logDebug (print_r ($_FILES, true), "uploadAction");
           foreach ($files as $fieldname => $fileInfo)
           {
             if ($adapter->isUploaded () && $adapter->isValid ())
             {
               $dateiname = $fileInfo [name];
               $tmpfile = $fileInfo [tmp_name];
               $filetyp = $fileInfo [type];
               $error = $fileInfo  [error];
               $filesize = $fileInfo [size];
               $model = new Model_DbTable_MediaData ();
               $model->uploadMediaFile ($fileInfo, $mediaBeschreibung, $mediaTyp, $mediaID);
             }
           }
         } catch (Exception $e)
           {
             logError ($e->getMessage (), "tgr");
           }

       }

    */
    /**
     * löscht ein Medium
     *
     * @return void
     */
    public function delAction ()
    {
      Model_History::save2history ();
      $ID = $this->getRequest ()->getParam ('id');
      $model = new Model_DbTable_MediaData ();
      $rawData = $model->delMediaFile ($ID);
    }


    /**
     * speichert ein Medium
     *
     * @return void
     */
    public function saveAction ()
    {
      Model_History::save2history ();
      $mID = $this->getRequest ()->getParam ('id');
      $model = new Model_DbTable_MediaData ();
      $selectedField = $this->getRequest ()->getParam ('field');
      $dbField = $selectedField;
      $selectedValue = $this->getRequest ()->getParam ('value');
      ////logDebug ($selectedField." : ".$selectedValue, "saveAction");
      if ($mID > 0)
      {
        $model->saveMedia ($dbField, $selectedValue, $mID);
      }
    }

    /**
     * löscht alte Zombie-Einträge
     *
     * @return void
     */
    public function cleartableAction ()
    {
      $model = new Model_DbTable_MediaData ();
      $model->clearMediaTable ();
    }

    /**
     * left ein neues Medium an
     *
     * @return void
     */
    public function newAction ()
    {
      Model_History::save2history ();
      $mID = $this->getRequest ()->getParam ('id');
      $sessionNamespace = new Zend_Session_Namespace ();
      $anbieterData = $sessionNamespace->anbieterData;
      $anbieterID = $anbieterData ['anbieterID'];
      $model = new Model_DbTable_MediaData ();
      $new_mID = $model->newMediaFile ($anbieterID);
      $response = array('mediaID' => $new_mID);
      $this->_helper->json->sendJson ($response);
    }
  }

?>
