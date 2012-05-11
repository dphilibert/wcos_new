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
        $result = $soap_client->getProduktSpektrum (2); // alle
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
        $result = $soap_client->searchByProduktcode (1, 3701);
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
        $result = $soap_client->searchByName (1, 'weka');
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      print_r ($result);
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


    public function terminelisteAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getTermineListe (1057850);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "getTermineliste-Test");
    }


    public function termindetailsAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getTermineDetails (83);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "getTerminDetails-Test");
    }


    public function bildAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getBild (8803443, 3);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "getBild-Test");
    }


    public function videoAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getVideo (615320);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "getVideos-Test");
    }


    public function apAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getAnsprechpartnerListe (1057850);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "apAction-Test");
    }


    public function risevisitsAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->riseVisitCounter (8808980);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      //logDebug (print_r ($result, true), "riseVisitCounter-Test");
    }

    public function getlastchangedAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getLastActivities (1, 10);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "getlastchanged-Test");
    }


    public function newestAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getNewest (1, 10);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "getlastchanged-Test");
    }


    public function mostseenAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getMostSeen (1, 10);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "getlastchanged-Test");
    }

    public function pcnameAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getProduktCodeName (3912);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "getlastchanged-Test");
    }


    public function randomAction ()
    {
      $location_soap_wcos = "http://wcos/soap";
      //$result = $soap_client->searchAnbieter ('weka');
      try
      {
        $soap_client = new SoapClient(null, array('location' => $location_soap_wcos, 'uri' => 'wcos'));
        $result = $soap_client->getRandomAccounts (1,10);
      } catch (SoapFault $e)
      {
        logDebug (print_r ($e->getMessage (), true), "Exception geschmissen!");
      }
      logDebug (print_r ($result, true), "getlastchanged-Test");
    }
  }

?>
