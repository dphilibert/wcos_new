<?php

  /**
   * Klasse für den Webservice (Modul soap)
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class wcosWebservice
  {

    /**
     * Anbieter suchen
     *
     * @param string $searchPhrase Suchbegriff
     *
     * @return mixed
     */
    public function searchAnbieter ($searchPhrase)
    {
      $model = new Model_DbTable_AnbieterData ();
      $hits = $model->searchAnbieter ($searchPhrase);
      return $hits;
    }

    /**
     * liefert die Details eines Anbieters
     *
     * @param int $anbieterID AnbieterID
     *
     * @return array
     */
    public function getAnbieterDetails ($anbieterID)
    {
      $model = new Model_DbTable_AnbieterData ();
      $anbieterDetails = $model->getAnbieterDetails ($anbieterID);
      return $anbieterDetails;
    }

    /**
     * liefert eine Liste aller Ansprechpartner eines Anbieters
     *
     * @param int $anbieterID AnbieterID
     *
     * @return mixed
     */
    public function getAnsprechpartnerListe ($anbieterID)
    {
      $model = new Model_DbTable_AnsprechpartnerData ();
      $ansprechpartnerDetails = $model->getAnsprechpartnerList (NULL, $anbieterID);
      return $ansprechpartnerDetails;
    }

    /**
     * liefert ein Medium eines Anbieters oder ohne Anbieter-Selektion
     *
     * @param int $mediaID mediaID
     * @param int $anbieterID AnbieterID oder null (ohne Anbieter-Selektion)
     *
     * @return mixed
     */
    public function getMedia ($mediaID, $anbieterID = NULL)
    {
      $config = Zend_Registry::get ('config');
      $model = new Model_DbTable_MediaData ();
      $media = $model->getMedia ($mediaID, $anbieterID);
      foreach ($media as $key => $value)
      {
        $mediaFilePath = $config->uploads->path . '/' . $value ['mediaID'] . '.' . $value ['mediaExtension'];
        if (file_exists ($mediaFilePath))
        {
          $mediaFile = file_get_contents ($mediaFilePath);
        }
        $mediaData [$key] ['typ'] = $value ['mediatyp'];
        $mediaData [$key] ['filename'] = $value ['mediadatei'];
        $mediaData [$key] ['extension'] = $value ['mediaExtension'];
        $mediaData [$key] ['data'] = base64_encode ($mediaFile);
      }
      return $mediaData;
    }

    /**
     * liefert alle Medien zu einem Anbieter
     *
     * @param int $anbieterID AnbieterID
     * @param @depricated int $minimumTyp
     *
     * @return mixed
     */
    public function getAllMedia ($anbieterID = NULL, $minimumTyp = NULL)
    {
      $config = Zend_Registry::get ('config');
      $model = new Model_DbTable_MediaData ();
      $media = $model->getAllMedia ($anbieterID, $minimumTyp);
      foreach ($media as $key => $value)
      {
        $mediaFilePath = $config->uploads->path . '/' . $value ['mediaID'] . '.' . $value ['mediaExtension'];
        if (file_exists ($mediaFilePath))
        {
          $mediaFile = file_get_contents ($mediaFilePath);
        }
        $mediaData [$key] ['typ'] = $value ['mediatyp'];
        $mediaData [$key] ['beschreibung'] = $value ['beschreibung'];
        $mediaData [$key] ['filename'] = $value ['mediadatei'];
        $mediaData [$key] ['extension'] = $value ['mediaExtension'];
        $mediaData [$key] ['link'] = $value ['link'];
        $mediaData [$key] ['data'] = base64_encode ($mediaFile);
      }
      return $mediaData;
    }

    /**
     * liefert eine Liste aller Termine eines Anbieters
     *
     * @param int $anbieterID AnbieterID
     *
     * @return mixed
     */
    public function getTermineListe ($anbieterID)
    {
      $model = new Model_DbTable_TermineData ();
      $termineDetails = $model->getTermineList ($anbieterID);
      return $termineDetails;
    }

    /**
     * liefert die Details zu einem Termin
     *
     * @param $terminID
     */
    public function getTermineDetails ($terminID)
    {
      // TODO Funktionalität
    }

    /**
     * liefert das Firmenprofil eines Anbieters
     *
     * @param int $anbieterID AnbieterID
     *
     * @return mixed
     */
    public function getFirmenprofil ($anbieterID)
    {
      $model = new Model_DbTable_FirmenportraitData ();
      $firmenportrait = $model->getFirmenportrait ($anbieterID);
      return $firmenportrait [0];
    }

    /**
     * liefert alle Jobs eines Anbieters
     *
     * @param int $anbieterID AnbieterID
     *
     * @return array
     */
    public function getJobs ($anbieterID)
    {
      $model = new Model_DbTable_JobsData ();
      $jobs = $model->getJobs ($anbieterID);
      return $jobs;
    }

    /**
     * liefert eine Liste aller Whitepaper eines Anbieters
     *
     * @param int $anbieterID AnbieterID
     *
     * @return mixed
     */
    public function getWhitepaperListe ($anbieterID)
    {
      $model = new Model_DbTable_WhitepaperData ();
      $whitepaperDetails = $model->getWhitepaperList ($anbieterID);
      return $whitepaperDetails;
    }


    /**
     * liefert den Produktbaum
     *
     */
    public function getProduktbaum ($anbieterID)
    {
      $model = new Model_DbTable_ProduktcodesData();
      $produktcodesArray = $model->getProduktcodes ($anbieterID);
      foreach ($produktcodesArray as $key => $produktDatensatz)
      {
        $hauptbegriff = $produktDatensatz ['hauptbegriff'];
        $oberbegriff = $produktDatensatz ['oberbegriff'];
        $branchenname = $produktDatensatz ['branchenname'];
        $branchenname_nummer = $produktDatensatz ['branchenname_nummer'];
        $produktBaum [$hauptbegriff] [$oberbegriff] = $branchenname;
      }
      return $produktBaum;
      //logDebug (print_r ($produktcodesArray, true), "");
    }


    /**
     * liefert das Produktspektrum für das angegebene System
     *
     * @param $systemID
     *
     * @return mixed
     */
    public function getProduktSpektrum ($systemID)
    {
      $model = new Model_DbTable_ProduktcodesData();
      $produktcodesArray = $model->getProduktSpektrum ($systemID);
      $i = 0;
      foreach ($produktcodesArray as $key => $produktDatensatz)
      {
        $hauptbegriff = str_replace ('&', '&amp;amp;', $produktDatensatz ['hauptbegriff']);
        $oberbegriff = str_replace ('&', '&amp;amp;', $produktDatensatz ['oberbegriff']);
        $branchenname = str_replace ('&', '&amp;amp;', $produktDatensatz ['branchenname']);
        $branchenname_nummer = $produktDatensatz ['branchenname_nummer'];
        if (@$produktBaum [$hauptbegriff] [$oberbegriff] [$i - 1] ['ProduktcodeID'] != $branchenname_nummer)
        {
          $produktBaum [$hauptbegriff] [$oberbegriff] [$i] ['ProduktcodeID'] = $branchenname_nummer;
          $produktBaum [$hauptbegriff] [$oberbegriff] [$i] ['ProduktcodeName'] = $branchenname;
          $firmen4produktcode = $model->countFirmen4Produktcode ($systemID, $branchenname_nummer);
          $produktBaum [$hauptbegriff] [$oberbegriff] [$i] ['Anzahl Firmen'] = $firmen4produktcode ['anzahl'];
          $i++;
        }
      }
      //logDebug (print_r ($produktBaum, true), "IndexController::getProduktSpektrum");
      return $produktBaum;
      //logDebug (print_r ($produktcodesArray, true), "");
    }

    /**
     * liefert die Stammdaten zu einer vmKundennummer aus der Stammdaten-Tabelle
     *
     * @param $vmKundennummer
     */
    public function getStammdaten ($vmKundennummer)
    {
      $model = new Model_DbTable_StammdatenData();
      $stammdaten = $model->getStammdaten ($vmKundennummer);
      return $stammdaten;
    }

    /**
     * liefert ein Array von n-Premium-Einträgen für das angegebene System, absteigend nach Häufigkeit der Anzeige sortiert
     *
     * @param $systemID
     * @param $anzahlDerEintraege
     */
    public function getMostSeen ($systemID, $anzahlDerEintraege)
    {
      // TODO Funktionalität
    }


    /**
     * liefert ein Array von n-Premium-Einträgen für das angegebene System, absteigend nach Erstelldatum sortiert
     *
     * @param $systemID
     * @param $anzahlDerEintraege
     */
    public function getNewest ($systemID, $anzahlDerEintraege)
    {
      // TODO Funktionalität
    }

    /**
     * liefert ein Array von n-Premium-Einträgen für das angegebene System, absteigend nach Datum der letzten Änderung sortiert
     *
     * @param $systemID
     * @param $anzahlDerEintraege
     */
    public function getLastActivities ($systemID, $anzahlDerEintraege)
    {
      // TODO Funktionalität
    }

    /**
     * liefert ein Array von n-Premium-Einträgen für das angegebene System, zufällig Anzeige sortiert
     *
     * @param $systemID
     * @param $anzahlDerEintraege
     */
    public function getRandomAccounts ($systemID, $anzahlDerEintraege)
    {
      // TODO Funktionalität
    }


    /**
     * sucht anhang des angegebenen Produktcodes die entsprechenden Firmen
     *
     * @param $systemID
     * @param $produktCode
     */
    public function searchByProduktcode ($systemID, $produktCode)
    {
      $pc_model = new Model_DbTable_ProduktcodesData();
      $pc_data = $pc_model->getFirmen4Produktcode ($systemID, $produktCode);
      foreach ($pc_data as $key => $dataset)
      {
        $vmKundennummer = $dataset ['vmKundennummer'];
        $anbieter_model = new Model_DbTable_AnbieterData();
        $anbieterData = $anbieter_model->getAnbieterByKundennummer ($vmKundennummer);
        $media_model = new Model_DbTable_MediaData();
        $media = $media_model->getAllMedia ($vmKundennummer, "FIRMENLOGO");
        $stammdaten_model = new Model_DbTable_StammdatenData();
        $stammdaten = $stammdaten_model->getStammdaten($vmKundennummer);
        $stammdaten = $stammdaten [0];
        //logDebug (count  ($media), "");
        $firmenlogo = NULL;
        $retData = NULL;
        if (count ($media) > 0)
        {
          $firmenlogo = $media [0] ['mediadatei'];
        }
        // TODO in $anbieterData noch die Stammdaten-Daten reinschreiben
        $premiumStatus = $anbieterData ['premiumLevel'];
        $retData ['Kundennummer'] = $vmKundennummer;
        $retData ['Name 1'] = $anbieterData ['firmenname'];
        $retData ['Name 2'] = ''; // TODO wo kommt der her?
        $retData ['Name 3'] = ''; // TODO wo kommt der her?
        $retData ['Name 4'] = ''; // TODO wo kommt der her?
        $retData ['Land'] = $stammdaten ['land'];
        $retData ['PLZ'] = $stammdaten ['plz'];
        $retData ['Ort'] = $stammdaten ['ort'];
        if ($firmenlogo != NULL)
        {
          $retData ['Logo'] = $firmenlogo;
        }
        if ($premiumStatus == 1)
        {
          $data ['PREMIUM'] [] = $retData;
        }
        else
        {
          $data ['STANDARD'] [] = $retData;
        }
      }
      return $data;
    }


    /**
     * sucht anhand des Firmennamens
     *
     * @param $systemID
     * @param $firmenName
     */
    public function searchByName ($systemID, $firmenName)
    {
      // TODO Funktionalität
    }


    /**
     * liefert den vollständigen Kundendatensatz
     *
     * @param $vmKundennummer
     */
    public function getAdress ($vmKundennummer)
    {
      // TODO Funktionalität
    }

    /**
     * liefert das Produktspektrum einer Firma
     *
     * @param $systemID
     * @param $vmKundennummer
     */
    public function getProduktSpektrum4Firma ($systemID, $vmKundennummer)
    {
      // TODO Funktionalität
    }
  }

  /**
   * IndexController des SOAP-Service
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Soap_IndexController extends Zend_Controller_Action
  {

    /**
     * disabled das Layouts und die Views und initiiert den SOAP-Handler
     *
     * @return void
     */
    public function indexAction ()
    {
      $this->_helper->layout ()->disableLayout ();
      $this->_helper->viewRenderer->setNoRender (true);
      if (isset ($_GET ['wsdl']))
      {
        //return the WSDL
        $this->handleWSDL ();
      }
      else
      {
        //handle SOAP request
        $this->handleSOAP ();
      }
    }

    /**
     * initiiert den SAOP-WSDL-Handler
     *
     * @return void
     */
    private function handleWSDL ()
    {
      $autodiscover = new Zend_Soap_AutoDiscover ();
      $autodiscover->setClass ('wcosWebservice');
      $autodiscover->handle ();
    }

    /**
     * initiiert den SOAP-Handler ohne WSDL
     *
     * @return void
     */
    private function handleSOAP ()
    {
      $soap = new Zend_Soap_Server (null, array('uri' => 'http://172.28.21.1/zbvs/public/soap/index'));
      $soap->setClass ('wcosWebservice');
      $soap->handle ();
    }
  }

?>
