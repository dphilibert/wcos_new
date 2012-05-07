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
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getProduktSpektrum (0, 16319); // alle
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "Produktcodes-Test");
    }


    public function searchbyproduktcodeAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->searchByProduktcode (1, 3366);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "Produktcodes-Test");
    }

    public function searchbyfirmennameAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->searchByName (0, 'geh');
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "searchbyfirmenname-Test");
    }


    public function getadressAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getAdress (8817778);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "getAdress-Test");
    }


    public function profilAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getFirmenprofil (8891886);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "getFirmenprofil-Test");
    }

    public function whitepaperAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getWhitepaper (9033401);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "getWhitepaper-Test");
    }
  }

?>
