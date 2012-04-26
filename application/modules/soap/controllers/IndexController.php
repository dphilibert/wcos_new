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
        if (file_exists ($mediaFilePath)) {
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
        if (file_exists ($mediaFilePath)) {
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
     * liefert das Firmenportrait eines Anbieters
     *
     * @param int $anbieterID AnbieterID
     *
     * @return mixed
     */
    public function getFirmenportrait ($anbieterID)
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
      $produktcodesArray = $model->getProduktcodes($anbieterID);
      foreach ($produktcodesArray as $key => $produktDatensatz)
      {
        $hauptbegriff = $produktDatensatz ['hauptbegriff'];
        $oberbegriff = $produktDatensatz ['oberbegriff'];
        $branchenname = $produktDatensatz ['branchenname'];
        $branchenname_nummer = $produktDatensatz ['branchenname_nummer'];

        $produktBaum [$hauptbegriff] [$oberbegriff]  = $branchenname;
      }
      return $produktBaum;
      //logDebug (print_r ($produktcodesArray, true), "");

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
//$ws = new wcosWebservice ();
//$ws->searchAnbieter ('K');
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
