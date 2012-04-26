<?php

  /**
   * testing Index
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Testing_IndexController extends Zend_Controller_Action
  {

    public function init ()
    {
      $this->_helper->_layout->disableLayout ();
      $this->_helper->viewRenderer->setNoRender (true);
    }

    /**
     * leitet auf die Login-Seite um
     *
     * @return void
     *
     */
    public function indexAction ()
    {
      $extAPI = new Model_ExtAPI_Verlagsmanager();
      $dataSet = $extAPI->searchAddressByKundennummer ('1057850');
      logDebug (print_r ($dataSet, true), "tgr");
    }


    /**
     * Testszenario für SOAP-API-Test
     *
     */
    public function soapAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
      //$result = $soap_client->searchAnbieter ('weka');
      $result = $soap_client->getAnsprechpartnerliste (1057850);
      logDebug (print_r ($result, true), "SOAP-Test");
    }


    /**
     * Testszenario für Produktcodes
     */
    public function produkteAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
      //$result = $soap_client->searchAnbieter ('weka');
      $result = $soap_client->getProduktbaum (16319); // Glyn

      logDebug (print_r ($result, true), "Produktcodes-Test");
    }
  }

?>
