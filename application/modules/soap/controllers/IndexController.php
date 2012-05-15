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
      $ansprechpartner = NULL;
      $model = new Model_DbTable_AnsprechpartnerData ();
      $ansprechpartnerListe = $model->getAnsprechpartnerList (NULL, $anbieterID);
      //logDebug (print_r ($ansprechpartnerListe, true), "");
      if (count ($ansprechpartnerListe) > 0)
      {
        $i = 0;
        foreach ($ansprechpartnerListe as $ap)
        {
          $ansprechpartner [$i] ['Vorname'] = $ap ['vorname'];
          $ansprechpartner [$i] ['Name'] = $ap ['nachname'];
          $ansprechpartner [$i] ['Position'] = $ap ['position'];
          $ansprechpartner [$i] ['Abteilung'] = $ap ['abteilung'];
          $ansprechpartner [$i] ['Telefon'] = $ap ['telefon'];
          $ansprechpartner [$i] ['Telefax'] = $ap ['telefax'];
          $ansprechpartner [$i] ['E-Mail'] = $ap ['email'];
          $mediaID = $ap ['mediaID'];
          $media_model = new Model_DbTable_MediaData();
          if ($mediaID != '')
          {
            $medium = $media_model->getMedia ($mediaID);
            if (count ($medium) > 0)
            {
              $ansprechpartner [$i] ['Bild'] = $medium [0] ['mediaID'] . "." . $medium [0] ['mediaExtension'];
            }
          }
          $i++;
        }
      }
      //logDebug (print_r ($ap, true), "");
      return $ansprechpartner;
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
     * liefert ein angegebenes oder alle Bilder zu einem Anbieter
     *
     * @param $anbieterID
     * @param null $bildNummer
     *
     * @return array
     */
    public function getBild ($anbieterID, $bildNummer = NULL)
    {
      $bilder = NULL;
      $model = new Model_DbTable_MediaData ();
      $media = $model->getAllMedia ($anbieterID, "BILD");
      //logDebug (print_r ($media, true), "");
      $i = 1;
      foreach ($media as $bild)
      {
        $bilder [$i] ['Dateiname'] = $bild ['mediaID'] . "." . $bild ['mediaExtension'];
        $bilder [$i] ['Bildbeschreibung'] = $bild ['desc'];
        $bilder [$i] ['URL'] = $bild ['link'];
        $i++;
      }
      if ($bildNummer != NULL)
      {
        return $bilder [$bildNummer];
      }
      return $bilder;
    }


    public function getProduktcodeName ($produktCode)
    {
      $retData = NULL;
      $model = new Model_DbTable_ProduktcodesData();
      $data = $model->getProduktcodeName ($produktCode);
      if (count ($data) > 0)
      {
        $retData ['Produktcodename'] = $data ['branchenname'];
      }
      return $retData;
    }

    /**
     * liefert ein angegebenes oder alle Videos zu einem Anbieter
     *
     * @param $anbieterID
     * @param null $videoNummer
     *
     * @return array
     */
    public function getVideo ($anbieterID, $videoNummer = NULL)
    {
      $videos = NULL;
      $model = new Model_DbTable_MediaData ();
      $media = $model->getAllMedia ($anbieterID, "VIDEO");
      //logDebug (print_r ($media, true), "");
      $i = 1;
      foreach ($media as $video)
      {
        $videos [$i] ['Dateiname'] = $video ['mediaID'] . "." . $video ['mediaExtension'];
        $videos [$i] ['Bildbeschreibung'] = $video ['desc'];
        $videos [$i] ['URL'] = $video ['link'];
        if ($video ['embed'] != '')
        {
          $videos [$i] ['Embedcode'] = $video ['embed'];
        }
        $i++;
      }
      if ($videoNummer != NULL)
      {
        return $videos [$videoNummer];
      }
      return $videos;
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
      $termine = NULL;
      $model = new Model_DbTable_TermineData ();
      $termineListe = $model->getTermineList ($anbieterID);
      if (count ($termineListe) > 0)
      {
        $i = 0;
        foreach ($termineListe as $termin)
        {
          $termine [$i] ['Termin-Art'] = $termin ['terminTyp'];
          $termine [$i] ['Termin-ID'] = $termin ['termineID'];
          $termine [$i] ['Name'] = $termin ['name'];
          $termine [$i] ['Beginn'] = $termin ['beginn'];
          $termine [$i] ['Ende'] = $termin ['ende'];
          $termine [$i] ['Ort'] = $termin ['ort'];
          $mediaID = $termin ['mediaID'];
          $media_model = new Model_DbTable_MediaData();
          $medium = $media_model->getMedia ($mediaID);
          if (count ($medium) > 0)
          {
            $termine [$i] ['Logo'] = $medium [0] ['mediaID'] . "." . $medium [0] ['mediaExtension'];
          }
          $i++;
        }
      }
      return $termine;
    }

    /**
     * liefert die Details zu einem Termin
     *
     * @param $terminID
     */
    public function getTermineDetails ($terminID)
    {
      $termin = NULL;
      $model = new Model_DbTable_TermineData();
      $data = $model->getTermin ($terminID);
      //logDebug (print_r ($data, true), "");
      if (count ($data) > 0)
      {
        $data = $data [0];
        $termin ['Termin-Art'] = $data ['terminTyp'];
        $termin ['Termin-ID'] = $data ['termineID'];
        $termin ['Name'] = $data ['name'];
        $termin ['Beginn'] = $data ['beginn'];
        $termin ['Ende'] = $data ['ende'];
        $termin ['Ort'] = $data ['ort'];
        $termin ['Kurzbeschreibung'] = $data ['teaser'];
        $termin ['Beschreibung'] = $data ['beschreibung'];
        $mediaID = $data ['mediaID'];
        $media_model = new Model_DbTable_MediaData();
        $medium = $media_model->getMedia ($mediaID);
        if (count ($medium) > 0)
        {
          $termin ['Logo'] = $medium [0] ['mediaID'] . "." . $medium [0] ['mediaExtension'];
        }
      }
      return $termin;
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
      $data = NULL;
      $model = new Model_DbTable_FirmenportraitData ();
      $modelData = $model->getFirmenportrait ($anbieterID);
      //logDebug (print_r ($firmenportrait, true), "");
      $firmenportrait = $modelData [0];
      if (count ($firmenportrait) > 0)
      {
        $data ['Firmenbeschreibung'] = $firmenportrait ['firmenbeschreibung'];
        $data ['Produkte/Linecard'] = $firmenportrait ['produkte'];
        $data ['Firmenausrichtung'] = $firmenportrait ['firmenausrichtung'];
        $data ['Dienstleistungen'] = $firmenportrait ['dienstleistungen'];
        $data ['Präsenz'] = $firmenportrait ['praesenz'];
        $data ['Zielmärkte'] = $firmenportrait ['zielmaerkte'];
        $data ['Standorte/Lager'] = $firmenportrait ['standorte'];
        $data ['Qualitätsmanagement'] = $firmenportrait ['qualitaetsmanagement'];
        $data ['Gründungsjahr'] = $firmenportrait ['gruendungsjahr'];
        $data ['Mitarbeiter'] = $firmenportrait ['mitarbeiter'];
      }
      return $data;
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


    public function getWhitepaper ($anbieterID)
    {
      $whitepaper = NULL;
      $wpListe = NULL;
      $wpListe = $this->getWhitepaperListe ($anbieterID);
      if (count ($wpListe) > 0)
      {
        $i = 0;
        foreach ($wpListe as $key => $wp)
        {
          $whitepaper [$i] ['Titel'] = $wp ['whitepaper_beschreibung'];
          $whitepaper [$i] ['URL'] = $wp ['whitepaper_link'];
          $i++;
        }
        return $whitepaper;
      }
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
     * liefert das Produktspektrum für das angegebene System und ggf. eine Kundennummer
     *
     * @param $systemID
     *
     * @return mixed
     */
    public function getProduktSpektrum ($systemID, $vmKundennummer = NULL)
    {
      $produktBaum = NULL;
      $model = new Model_DbTable_ProduktcodesData();
      $produktcodesArray = $model->getProduktSpektrum ($systemID, $vmKundennummer);
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
      $retData = NULL;
      $model = new Model_DbTable_AnbieterData();
      $data = $model->getMostSeen ($systemID, $anzahlDerEintraege);
      if (count ($data) > 0)
      {
        $i = 0;
        foreach ($data as $anbieter)
        {
          $retData [$i] ['Kundennummer'] = $anbieter ['vmKundennummer'];
          $i++;
        }
      }
      return $retData;
    }


    /**
     * liefert ein Array von n-Premium-Einträgen für das angegebene System, absteigend nach Erstelldatum sortiert
     *
     * @param $systemID
     * @param $anzahlDerEintraege
     */
    public function getNewest ($systemID, $anzahlDerEintraege)
    {
      $retData = NULL;
      $model = new Model_DbTable_AnbieterData();
      $data = $model->getNewest ($systemID, $anzahlDerEintraege);
      if (count ($data) > 0)
      {
        $i = 0;
        foreach ($data as $anbieter)
        {
          $retData [$i] ['Kundennummer'] = $anbieter ['anbieterID'];
          $i++;
        }
      }
      return $retData;
    }

    /**
     * liefert ein Array von n-Premium-Einträgen für das angegebene System, absteigend nach Datum der letzten Änderung sortiert
     *
     * @param $systemID
     * @param $anzahlDerEintraege
     */
    public function getLastActivities ($systemID, $anzahlDerEintraege)
    {
      $retData = NULL;
      $model = new Model_DbTable_AnbieterData();
      $data = $model->getLastChanged ($systemID, $anzahlDerEintraege);
      if (count ($data) > 0)
      {
        $i = 0;
        foreach ($data as $anbieter)
        {
          $retData [$i] ['Kundennummer'] = $anbieter ['anbieterID'];
          $i++;
        }
      }
      return $retData;
    }

    /**
     * liefert ein Array von n-Premium-Einträgen für das angegebene System, zufällig Anzeige sortiert
     *
     * @param $systemID
     * @param $anzahlDerEintraege
     */
    public function getRandomAccounts ($systemID, $anzahlDerEintraege)
    {
      $data = NULL;
      $model = new Model_DbTable_AnbieterData();
      if ($systemID == 0)
      {
        $systemID = NULL;
      }
      $resData = $model->getAnbieterRandom ($systemID, $anzahlDerEintraege);
      //logDebug (print_r ($resData, true), "");
      if (count ($resData) > 0)
      {
        foreach ($resData ['hits'] as $key => $dataset)
        {
          $medium = NULL;
          $vmKundennummer = $dataset ['anbieterID'];
          $anbieter_model = new Model_DbTable_AnbieterData();
          $anbieterData = $anbieter_model->getAnbieterByKundennummer ($vmKundennummer);
          $media_model = new Model_DbTable_MediaData();
          $media = $media_model->getAllMedia ($vmKundennummer, "FIRMENLOGO");
          $stammdaten_model = new Model_DbTable_StammdatenData();
          $stammdaten = $stammdaten_model->getStammdaten ($vmKundennummer);
          $stammdaten = $stammdaten [0];
          //logDebug (count  ($media), "");
          $firmenlogo = NULL;
          $retData = NULL;
          if (count ($media) > 0)
          {
            $firmenlogo = $media [0] ['mediaID'] . "." . $media [0] ['mediaExtension'];
          }
          $premiumStatus = $anbieterData ['premiumLevel'];
          $retData ['Kundennummer'] = $vmKundennummer;
          $retData ['Name 1'] = $anbieterData ['firmenname'];
          $retData ['Name 2'] = '';
          $retData ['Name 3'] = '';
          $retData ['Name 4'] = '';
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
    }

    /**
     * erzeugt einen Eintrag in der statsVisits-Tabelle
     *
     * @param $anbieterID
     *
     * @return 0 kein Fehler
     */
    public
    function riseVisitCounter ($anbieterID)
    {
      $model = new Model_DbTable_GeneralData();
      $model->saveVisit ($anbieterID);
      return 0;
    }

    /**
     * sucht anhang des angegebenen Produktcodes die entsprechenden Firmen
     *
     * @param $systemID
     * @param $produktCode
     */
    public
    function searchByProduktcode ($systemID, $produktCode)
    {
      $data = NULL;
      $pc_model = new Model_DbTable_ProduktcodesData();
      $pc_data = $pc_model->getFirmen4Produktcode ($systemID, $produktCode);
      if (count ($pc_data) > 0)
      {
        foreach ($pc_data as $key => $dataset)
        {
          $vmKundennummer = $dataset ['vmKundennummer'];
          $anbieter_model = new Model_DbTable_AnbieterData();
          $anbieterData = $anbieter_model->getAnbieterByKundennummer ($vmKundennummer);
          $media_model = new Model_DbTable_MediaData();
          $media = $media_model->getAllMedia ($vmKundennummer, "FIRMENLOGO");
          $stammdaten_model = new Model_DbTable_StammdatenData();
          $stammdaten = $stammdaten_model->getStammdaten ($vmKundennummer);
          $stammdaten = $stammdaten [0];
          //logDebug (count  ($media), "");
          $firmenlogo = NULL;
          $retData = NULL;
          if (count ($media) > 0)
          {
            $firmenlogo = $medium [0] ['mediaID'] . "." . $medium [0] ['mediaExtension'];
          }
          $premiumStatus = $anbieterData ['premiumLevel'];
          $retData ['Kundennummer'] = $vmKundennummer;
          $retData ['Name 1'] = $anbieterData ['firmenname'];
          $retData ['Name 2'] = '';
          $retData ['Name 3'] = '';
          $retData ['Name 4'] = '';
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
    }

    /**
     * sucht anhand des Firmennamens
     *
     * @param $systemID
     * @param $firmenName
     */
    public function searchByName ($systemID, $firmenName)
    {
      $data = NULL;
      $model = new Model_DbTable_AnbieterData();
      if ($systemID == 0)
      {
        $systemID = NULL;
      }
      $resData = $model->searchAnbieter ($firmenName, $systemID);
      //logDebug (print_r ($resData, true), "");
      if (count ($resData) > 0)
      {
        foreach ($resData ['hits'] as $key => $anbieterData)
        {
          $vmKundennummer = $anbieterData ['anbieterID'];
          $anbieter_model = new Model_DbTable_AnbieterData();
          //        $anbieterData = $anbieter_model->getAnbieterByKundennummer ($vmKundennummer);
          //  $media_model = new Model_DbTable_MediaData();
          // $media = $media_model->getAllMedia ($vmKundennummer, "FIRMENLOGO");
          /*
          $stammdaten_model = new Model_DbTable_StammdatenData();
          $stammdaten = $stammdaten_model->getStammdaten ($vmKundennummer);
          $stammdaten = $stammdaten [0];
          */
          //logDebug (count  ($media), "");
          $firmenlogo = NULL;
          $retData = NULL;
          if (array_key_exists ('mediaID', $anbieterData) && $anbieterData ['mediaID'] != NULL)
          {
            $firmenlogo = $anbieterData ['mediaID'] . "." . $anbieterData ['mediaExtension'];
          }
          $premiumStatus = $anbieterData ['premiumLevel'];
          $retData ['Kundennummer'] = $vmKundennummer;
          if (!array_key_exists ('name1', $retData) || $retData ['name1'] == NULL)
          {
            $retData ['Name 1'] = $anbieterData ['firmenname'];
          }
          else
          {
            $retData ['Name 1'] = $anbieterData ['name1'];
          }
          $retData ['Name 2'] = $anbieterData ['name2'];
          ;
          $retData ['Name 3'] = $anbieterData ['name3'];
          $retData ['Name 4'] = $anbieterData ['name4'];
          $retData ['Land'] = $anbieterData ['land'];
          $retData ['PLZ'] = $anbieterData ['plz'];
          $retData ['Ort'] = $anbieterData ['ort'];
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
    }

    /**
     * liefert den vollständigen Kundendatensatz
     *
     * @param $vmKundennummer
     */
    public function getAdress ($vmKundennummer)
    {
      $data = NULL;
      $model = new Model_DbTable_StammdatenData();
      $resData = $model->getStammdaten ($vmKundennummer);
      logDebug (print_r ($resData, true), "resData");
      if (count ($resData) > 0)
      {
        $anbieter_model = new Model_DbTable_AnbieterData();
        $anbieterData = $anbieter_model->getAnbieterByKundennummer ($vmKundennummer);
        $media_model = new Model_DbTable_MediaData();
        $media = $media_model->getAllMedia ($vmKundennummer, "FIRMENLOGO");
        $stammdaten_model = new Model_DbTable_StammdatenData();
        $stammdaten = $stammdaten_model->getStammdaten ($vmKundennummer);
        $stammdaten = $stammdaten [0];
        //logDebug (count  ($media), "");
        $firmenlogo = NULL;
        $retData = NULL;
        if (count ($media) > 0)
        {
          $media = $media [0];
          $firmenlogo = $media ['mediaID'] . "." . $media ['mediaExtension'];
        }
        $retData ['Premium'] = $anbieterData ['premiumLevel'];
        $retData ['Name 1'] = $anbieterData ['firmenname'];
        $retData ['Name 2'] = '';
        $retData ['Name 3'] = '';
        $retData ['Name 4'] = '';
        $retData ['Land'] = $stammdaten ['land'];
        $retData ['PLZ'] = $stammdaten ['plz'];
        $retData ['Ort'] = $stammdaten ['ort'];
        $retData ['Straße'] = $stammdaten ['strasse'];
        $retData ['Hausnummer'] = $stammdaten ['hausnummer'];
        $retData ['Telefon'] = $stammdaten ['fon'];
        $retData ['Telefax'] = $stammdaten ['fax'];
        $retData ['E-Mail'] = $stammdaten ['email'];
        $retData ['Internetadresse'] = $stammdaten ['www'];
        if ($firmenlogo != NULL)
        {

          $retData ['Logo'] = $firmenlogo;
        }
        return $retData;
      }
    }

    /**
     * liefert das Produktspektrum einer Firma
     *
     * @param $systemID
     * @param $vmKundennummer
     */
    public
    function getProduktSpektrum4Firma ($systemID, $vmKundennummer)
    {
      return $this->getProduktSpektrum ($systemID, $vmKundennummer);
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
