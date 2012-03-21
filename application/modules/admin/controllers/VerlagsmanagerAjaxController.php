<?php

  /**
   * Modul Admin, Verlagsmanager Ajax-Funktionen
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Admin_VerlagsmanagerAjaxController extends Zend_Controller_Action
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
     * startet den Import
     *
     * @return void
     */
    public function startImport ()
    {
      $model = new Model_DbTable_AnbieterData ();
      $model->createImportTable ();
      $model->importData ();
    }

    /**
     * Action zum Import-Start
     *
     * @return void
     */
    public function importAction ()
    {
//TODO Import durchführen und Status (%) in eine Tabelle schreiben mit unqiuem Ident-Hash.
      $this->startImport ();
    }

    /**
     * liefert den Import-Status per json
     *
     * @return void
     */
    public function getimportstatusAction ()
    {
//TODO  Import-Status mit unique-Iden-Hash aus der Tabelle auslesen und weitergeben
      $param = $this->getRequest ()->getParam ('progress');
      $param = $param + 100;
      if ($param > 100) {
        $param = "end";
      } // "end" ist wichtig weil sonst die Rekustion auf js-Seite nicht beendet wird
      $this->_helper->json->sendJson ($param);
    }

    /**
     * Testfunktion für die VM-API
     *
     * @return void
     */
    public function apitestAction ()
    {
      $view = new Zend_View();
      $view->setScriptPath ('/xml/');
      $xml = $view->render ('getadress.xml');
      $foo = new Model_ExtAPI_Verlagsmanager();
      //logDebug (print_r ($foo->getAdress('WEKA', $xml), true), "apitest");
      /*
            *
           $client = new SoapClient("http://217.111.48.221:18080/4DWSDL", array ('login' => 'TGrahammer',
                                                                                 'password' => 'rxyust56#',
                                                                                ));

           $options = '<?xml version="1.0" encoding="ISO-8859-1"?>
                       <vm>
                          <query>
                              <keyName>WEKA</keyName>
                          </query>
                       </vm>';

           $result = $client->ws_find_address (100,$options);

           $xml = $result ['GP_resultList'];


           // interessant für hallo-ping

           //$result = $client->ws_VMVersion ();

           $xmlObj = simplexml_load_string($xml);

           $ret = $xmlObj->addressPool->addressPool->lastName;

           //logDebug (print_r ($ret, true), "apitestAction");
            //$ret = "foooooooo";
           $this->_helper->json->sendJson ($ret);

      */
    }
  }

?>