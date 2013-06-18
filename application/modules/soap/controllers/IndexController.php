<?php

  /**
   * WCOS-SOAP-SERVICE
   *
   */
  class wcosWebservice
  {
    /**
     * System-ID
     * @var int 
     */
    var $system;
    
    /**
     * Initialisierung
     * 
     * @param int $system_id System-ID 
     */
    public function __construct ($system_id)
    {
      if (empty ($system_id))
        throw new Zend_Exception ('fehlende System-ID');
      
      $this->system = $system_id;      
    }        
    
    /**
     * Model Instanz holen
     * 
     * @param string $name Model-Suffix
     * @param int|void $anbieterID Anbieter-ID
     * @return \model 
     */
    public function model ($name, $anbieterID = NULL)
    {
      $model = 'Model_DbTable_'.$name;
      return new $model (array ('system_id' => $this->system, 'provider_id' => $anbieterID));
    }        
    
    /**
     * Anbieter suchen
     *
     * @param string $searchPhrase Suchbegriff
     * @return array
     */
    public function searchAnbieter ($searchPhrase)
    {
      $model = $this->model ('AnbieterData');
      return $model->searchAnbieter ($searchPhrase);
    }

    /**
     * liefert die Details eines Anbieters
     *
     * @param int $anbieterID AnbieterID
     * @return array
     */
    public function getAnbieterDetails ($anbieterID)
    {
      $model = $this->model ('AnbieterData', $anbieterID);
      return $model->getAnbieterDetails ();      
    }

    /**
     * liefert eine Liste aller Ansprechpartner eines Anbieters
     *
     * @param int $anbieterID AnbieterID
     * @return array
     */
    public function getAnsprechpartnerListe ($anbieterID)
    {      
      $model = $this->model ('AnsprechpartnerData', $anbieterID);      
      $contact_list = $model->get_contacts_list ();      
      $contacts = array ();
      
      $model = $this->model ('MediaData', $anbieterID);
      if (!empty ($contact_list))
      {        
        foreach ($contact_list as $key => $contact)
        {
          $contacts [$key]['Vorname'] = $contact ['vorname'];
          $contacts [$key]['Name'] = $contact ['nachname'];
          $contacts [$key]['Position'] = $contact ['position'];
          $contacts [$key]['Abteilung'] = $contact ['abteilung'];
          $contacts [$key]['Telefon'] = $contact ['telefon'];
          $contacts [$key]['Telefax'] = $contact ['telefax'];
          $contacts [$key]['E-Mail'] = $contact ['email'];  
          if (!empty ($contact ['media']))
            $contacts [$key]['Bild'] = $contact ['media'];                              
        }
      }      
      return $contacts;
    }

    /**
     * liefert ein Medium eines Anbieters oder ohne Anbieter-Selektion
     *
     * @param int $mediaID mediaID
     * @param int $anbieterID AnbieterID oder null (ohne Anbieter-Selektion)
     * @return array
     */
    public function getMedia ($mediaID, $anbieterID = NULL)
    {
      $model = $this->model ('MediaData', $anbieterID);                  
      $media = $model->get_media_row ($mediaID);     
      $media_info = pathinfo ($media ['media']);
      
      $provider_media = array ();                   
      $provider_media ['typ'] = $media ['media_type'];
      $provider_media ['filename'] = $media_info ['filename'];
      $provider_media ['extension'] = $media_info ['extension'];
      $provider_media ['data'] = (file_exists (UPLOAD_PATH.$media ['media'])) ? base64_encode (file_get_contents (UPLOAD_PATH.$media ['media'])) : '';
      
      return array ($provider_media);
    }

    /**
     * liefert ein angegebenes oder alle Bilder zu einem Anbieter
     *
     * @param $anbieterID Anbieter-ID
     * @param int|void $bildNummer Media-ID
     * @return array
     */
    public function getBild ($anbieterID, $bildNummer = NULL)
    {
      $model = $this->model ('MediaData', $anbieterID);      
      $media = $model->get_media_list (1);
      $images = array ();     
      
      if (!empty ($media))
      {  
        if (!empty ($bildNummer))                      
          $media_data [0] = $media [(($bildNummer < 0) ? 1 : $bildNummer) - 1];        
        else      
          $media_data = $media;

        foreach ($media_data as $image)
        {
          if (!empty ($image ['media']))
          {  
            $images [] = array (
              'Dateiname' => $image ['media'],
              'Bildbeschreibung' => $image ['beschreibung'],
              'bu' => $image ['text'],  
              'URL' => $image ['link']  
            );
          }
        }
      }
      
      return (count ($images) == 1) ? $images [0] : $images;
    }

    /**
     * liefert den Namen zu einem Produkt-Code
     * 
     * @param int $produktCode Produkt-Code
     * @return array
     */
    public function getProduktcodeName ($produktCode)
    {
      $model = $this->model ('ProduktcodesData');
      $name = $model->get_product_name ($produktCode);
            
      return array ('Produktcodename' => $name);
    }

    /**
     * liefert ein angegebenes oder alle Videos zu einem Anbieter
     *
     * @param int $anbieterID Anbieter-ID
     * @param int|void $videoNummer Media-ID
     * @return array
     */
    public function getVideo ($anbieterID, $videoNummer = NULL)
    {
      $model = $this->model ('MediaData', $anbieterID);
      $media = $model->get_media_list (2);
      $videos = array (); 
      
      if (!empty ($media))
      {  
        if (!empty ($videoNummer))                      
          $media_data [0] = $media [(($videoNummer < 0) ? 1 : $videoNummer) - 1];        
        else      
          $media_data = $media;

        foreach ($media_data as $video)
        {
          $check = file_exists (UPLOAD_PATH.$video ['media']);
          $videos [] = array (
            'Dateiname' => ($check) ? $video ['media'] : '',
            'Bildbeschreibung' => $video ['beschreibung'],
            'URL' => $video ['link'],
            'Embedcode' => (!$check) ? $video ['media'] : ''  
          );                
        }
      }
      
      return (count ($videos) == 1) ? $videos [0] : $videos;
    }

    /**
     * liefert ein Video-Vorschau-Bild
     * 
     * @param int $anbieterID Anbieter-ID
     * @param string $beschreibung Beschreibung
     * @return string Dateiname
     */
    public function getStartbild ($anbieterID, $beschreibung)
    {
      $model = $this->model ('MediaData', $anbieterID);      
      return $model->get_video_teaser ($beschreibung);
    }

    /**
     * liefert alle Medien zu einem Anbieter
     *
     * @param int $anbieterID Anbieter-ID
     * @param @deprecated int $minimumTyp
     * @return array
     */
    public function getAllMedia ($anbieterID, $minimumTyp = NULL)
    {
      $model = $this->model ('MediaData', $anbieterID);                  
      $images = $model->get_media_list (1);
      $videos = $model->get_media_list (2);
      $all_media = array_merge ($images, $videos);
            
      $media_data = array ();
      foreach ($all_media as $key => $media)
      {        
        $media_info = pathinfo ($media ['media']);
        $check = file_exists (UPLOAD_PATH.$media ['media']);
                
        $media_data [$key] = array (
          'typ' => $media ['media_type'],
          'beschreibung' => $media ['beschreibung'],
          'bu' => $media ['text'],  
          'filename' => ($check) ? $media_info ['filename'] : '',
          'extension' => ($check) ? $media_info ['extension'] : '',
          'link' => $media ['link'],
          'data' => ($check) ? base64_decode (file_get_contents (UPLOAD_PATH.$media ['media'])) : '',
          'Embedcode' => (!$check) ? $media ['media'] : ''  
        );                       
      }
      
      return $media_data;
    }

    /**
     * liefert eine Liste aller Termine eines Anbieters
     *
     * @param int $anbieterID Anbieter-ID
     * @return array
     */
    public function getTermineListe ($anbieterID)
    {
      $model = $this->model ('TermineData', $anbieterID);
      $dates = $model->get_dates_list ();
      
      $model = $this->model ('MediaData', $anbieterID);
      $appointments = array ();
      if (!empty ($dates))
      {        
        foreach ($dates as $key => $date)
        {
          $appointments [$key]['Termin-Art'] = $date ['typID'];
          $appointments [$key]['Termin-ID'] = $date ['id'];
          $appointments [$key]['Name'] = $date ['title'];
          $appointments [$key]['Beginn'] = $date ['beginn'];
          $appointments [$key]['Ende'] = $date ['ende'];
          $appointments [$key]['Ort'] = $date ['ort'];
          if (!empty ($date ['media']))
            $appointments [$key]['Logo'] = $date ['media'];                    
        }
      }
      
      return $appointments;
    }

    /**
     * liefert die Details zu einem Termin
     *
     * @param int $terminID Termin-ID
     * @return array
     */
    public function getTermineDetails ($terminID)
    {
      $model = $this->model ('TermineData');
      $date = $model->get_date ($terminID);
            
      $appointment = array ();
      if (!empty ($date))
      {        
        $appointment ['Termin-Art'] = $date ['typID'];
        $appointment ['Termin-ID'] = $date ['id'];
        $appointment ['Name'] = $date ['title'];
        $appointment ['Beginn'] = $date ['beginn'];
        $appointment ['Ende'] = $date ['ende'];
        $appointment ['Ort'] = $date ['ort'];
        $appointment ['Kurzbeschreibung'] = $date ['teaser'];
        $appointment ['Beschreibung'] = $date ['beschreibung'];
        
        $model = $this->model ('MediaData', $date ['anbieterID']);
        $media = $model->get_media (7, $date ['id']);
        if (!empty ($media ['media']))
          $appointment ['Logo'] = $media ['media'];                        
      }
      
      return $appointment;
    }

    /**
     * liefert das Firmenprofil eines Anbieters
     *
     * @param int $anbieterID Anbieter-ID
     * @return array
     */
    public function getFirmenprofil ($anbieterID)
    {
      $model = $this->model ('FirmenportraitData', $anbieterID);
      $profiles = $model->get_profile_list ();
      
      $profile_data = array ();
      if (!empty ($profiles))
      {
        $profile_data = array (
          'Firmenbeschreibung' => $profiles ['Umsatz']['text'], 
          'Produkte/Linecard' => $profiles ['Produkte']['text'],
          'Firmenausrichtung' => $profiles ['Firmenausrichtung']['text'],
          'Dienstleistungen' => $profiles ['Dienstleistungen']['text'],
          'Präsenz' => $profiles ['Präsenz']['text'],
          'Zielmärkte' => $profiles ['Zielmärkte']['text'],
          'Standorte/Lager' => $profiles ['Standorte']['text'],
          'Qualitätsmanagement' => $profiles ['Qualitätsmanagement']['text'],
          'Gründungsjahr' => $profiles ['Gründungsjahr']['text'],
          'Mitarbeiter' => $profiles ['Mitarbeiter']['text']  
        );                
      }
      return $profile_data;
    }

    /**
     * liefert alle Jobs eines Anbieters
     *
     * @param int $anbieterID Anbieter-ID
     * @return array
     */
    public function getJobs ($anbieterID)
    {
      return array ();
    }

    /**
     * liefert eine Liste aller Whitepaper eines Anbieters
     *
     * @param int $anbieterID AnbieterID
     * @return array
     */
    public function getWhitepaperListe ($anbieterID)
    {
      $model = $this->model ('WhitepaperData', $anbieterID);                           
      return $model->get_whitepaper_list ();
    }

    /**
     * liefert eine Liste aller Whitepaper eines Anbieters
     * 
     * @param int $anbieterID Anbieter-ID
     * @return array 
     */
    public function getWhitepaper ($anbieterID)
    {
      $model = $this->model ('WhitepaperData', $anbieterID);
      $whitepaper_list = $model->get_whitepaper_list ();
      
      $whitepapers = array ();
      if (!empty ($whitepaper_list))
      {        
        foreach ($whitepaper_list as $whitepaper)
        {
          $whitepapers [] = array (
            'Titel' => $whitepaper ['beschreibung'],
            'URL' => $whitepaper ['link']  
          );                    
        }
        
        return $whitepapers;
      }
    }

    /**
     * liefert den Produktbaum eines Anbieters
     *
     * @param int $anbieterID Anbieter-ID
     * @return array
     */
    public function getProduktbaum ($anbieterID)
    {
      $model = $this->model ('ProduktcodesData', $anbieterID);      
      $data = $model->get_provider_product_tree ();
      
      $provider_tree = array ();
      foreach ($data as $level1 => $level2_data)
      {
        foreach ($level2_data as $level2 => $level3_data)
        {
          foreach ($level3_data as $level3)
            $provider_tree [$level1][$level2][] = $level3 ['name'];
        }  
      }  
      
      return $provider_tree;
    }


    /**
     * liefert das Produktspektrum für das angegebene System und ggf. eine Kundennummer
     *
     * @param int $systemID System-ID
     * @param int $anbieterID Anbieter-ID
     * @return array
     */
    public function getProduktSpektrum ($systemID, $anbieterID = NULL)
    {
      $model = $this->model ('ProduktcodesData', $anbieterID);      
      $data = (empty ($anbieterID)) ? $model->get_product_tree () : $model->get_provider_product_tree ();
      
      $product_tree = array ();
      foreach ($data as $level1 => $level2_data)
      {
        foreach ($level2_data as $level2 => $level3_data)
        {
          foreach ($level3_data as $level3)
          {
            $count = $model->count_providers ($level3 ['code']);
            if ((empty ($anbieterID) AND !empty ($count)) OR !empty ($anbieterID))
            {  
              $product_tree [$level1][$level2][] = array (
                'ProduktcodeID' => $level3 ['code'],
                'ProduktcodeName' => $level3 ['name'],
                'Anzahl Firmen' => $count 
              );
            }
          }
        }  
      }  
      
      return $product_tree;      
    }

    /**
     * liefert die Stammdaten eines Anbieters
     *
     * @param int $anbieterID Anbieter-ID
     * @return array
     */
    public function getStammdaten ($anbieterID)
    {
      $model = $this->model('StammdatenData', $anbieterID);
      //todo: arraykeys nach bedarf uebersetzen
      return $model->get_address ();      
    }

    /**
     * liefert eine bestimmte Anzahl der am häufigsten angesehenen Premium-Anbieter
     *
     * @param int $systemID System-ID
     * @param  int $count Anzahl
     * @return array
     */
    public function getMostSeen ($systemID, $count)
    {
      $model = $this->model ('AnbieterData');
      $data = $model->getMostSeen ($count);
      
      $most_seen = array ();
      if (!empty ($data))
      {       
        foreach ($data as $provider)        
          $most_seen [] = array ('Kundennummer' => $provider ['anbieterID']);                    
      }
      
      return $most_seen;
    }


    /**
     * liefert eine bestimmte Anzahl der neuesten Premium-Anbieter
     *
     * @param int $systemID System-ID
     * @param int $count Anzahl
     * @return array
     */
    public function getNewest ($systemID, $count)
    {
      $model = $this->model ('AnbieterData');
      $data = $model->getNewest ($count);
      
      $newest = array ();
      if (!empty ($data))
      {       
        foreach ($data as $provider)        
          $newest [] = array ('Kundennummer' => $provider ['anbieterID']);                    
      }
      
      return $newest;
    }

    /**
     * liefert eine bestimmte Anzahl der zuletzt geänderten Premium-Anbieter 
     *
     * @param int $systemID System-ID
     * @param int $count Anzahl
     * @return array
     */
    public function getLastActivities ($systemID, $anzahlDerEintraege)
    {
      $model = $this->model ('AnbieterData');
      $data = $model->getLastChanged ($count);
      
      $last_changed = array ();
      if (!empty ($data))
      {       
        foreach ($data as $provider)        
          $last_changed [] = array ('Kundennummer' => $provider ['anbieterID']);                    
      }
      
      return $last_changed;
    }

    /**
     * liefert eine bestimmte Anzahl zufaelliger Premium-Anbieter
     *
     * @param int $systemID System-ID
     * @param int $count Anzahl
     * @return array
     */
    public function getRandomAccounts ($systemID, $count)
    {
      $model = $this->model ('AnbieterData');
      $data = $model->getAnbieterRandom ($count);
           
      $providers = array ();
      if (!empty ($data))
      {
        foreach ($data ['hits'] as $provider)
        {
           $model = $this->model ('MediaData', $provider ['anbieterID']);
           $logo = $model->get_media (5, $provider ['stammdatenID']);           
           $providers [] = array (
             'Kundennummer' => $provider ['anbieterID'],
             'Name 1' => $provider ['firmenname'],
             'Name 2' => '',
             'Name 3' => '',
             'Name 4' => '',
             'Land' => $provider ['land'],
             'PLZ' => $provider ['plz'],
             'Ort' => $provider ['ort'],
             'Logo' => $logo ['media']    
          );
        }                          
      }
      
      return array ('PREMIUM' => $providers);
    }

    /**
     * erhoeht den gesehen-Zaehler eines Anbieters
     *
     * @param int $anbieterID Anbieter-ID
     * @return 0
     */
    public function riseVisitCounter ($anbieterID)
    {
      $model = $this->model ('AnbieterData', $anbieterID);
      $model->riseVisitCounter ();            
      return 0;
    }

    /**
     * gibt die Anbieter zurueck, die dem Produktcode zugeordnet sind
     *
     * @param int $systemID System-ID
     * @param int $produktCode Produkt-Code
     * @return array
     */
    public function searchByProduktcode ($systemID, $produktCode)
    {
      $model = $this->model ('ProduktcodesData');
      $data = $model->get_providers ($produktCode);
      
      $providers = array ();
      if (!empty ($data))
      {
        foreach ($data as $provider)
        {
          $model = $this->model ('MediaData', $provider ['anbieterID']);
          $logo = $model->get_media (5, $provider ['stammdatenID']);        
          $type = ($provider ['premium'] == 1) ? 'PREMIUM' : 'STANDARD';
          $providers [$type][] = array (
            'Kundennummer' => $provider ['anbieterID'],
            'Name 1' => $provider ['firmenname'],
            'Name 2' => '',
            'Name 3' => '',
            'Name 4' => '',
            'Land' => $provider ['land'],
            'PLZ' => $provider ['plz'],
            'Ort' => $provider ['ort'],
            'Logo' => $logo ['media']      
          );                   
        }               
      }
      error_log (print_r ($providers, true), 3, '/home/daniel/www/wcos2/debug.log');
      return $providers;
    }

    /**
     * gibt alle Anbieter zurueck, deren Name den Suchbegriff enthaelt 
     *
     * @param int $systemID System-ID
     * @param string $firmenName Suchbegriff
     * @return array
     */
    public function searchByName ($systemID, $firmenName)
    {
      $model = $this->model ('AnbieterData');
      $data = $model->searchAnbieter ($firmenName);
                 
      $providers = array ();
      if (!empty ($data ['hits']))
      {
        foreach ($data ['hits'] as $provider)
        {            
          $type = ($provider ['premium'] == 1) ? 'PREMIUM' : 'STANDARD';
          $providers [$type][] = array (
            'Kundennummer' => $provider ['anbieterID'],
            'Name 1' => $provider ['firmenname'],
            'Name 2' => $provider ['name2'],
            'Name 3' => '',
            'Name 4' => '',
            'Land' => $provider ['land'],
            'PLZ' => $provider ['plz'],
            'Ort' => $provider ['ort'],
            'Logo' => $provider ['media']      
          );                   
        }               
      }
      
      return $providers;
    }

    /**
     * gibt alle Anbieter zurueck, deren Name mit dem Suchbegriff beginnt 
     * 
     * @param int $systemID System-ID
     * @param string $firmenName Suchbegriff
     * @return array
     */
    public function searchByNameInAlphabet ($systemID, $firmenName)
    {
      $model = $this->model ('AnbieterData');
      $data = $model->searchAnbieterInAlphabet ($firmenName);
      
      $providers = array ();
      if (!empty ($data ['hits']))
      {
        foreach ($data ['hits'] as $provider)
        {            
          $type = ($provider ['premium'] == 1) ? 'PREMIUM' : 'STANDARD';
          $providers [$type][] = array (
            'Kundennummer' => $provider ['anbieterID'],
            'Name 1' => $provider ['firmenname'],
            'Name 2' => $provider ['name2'],
            'Name 3' => '',
            'Name 4' => '',
            'Land' => $provider ['land'],
            'PLZ' => $provider ['plz'],
            'Ort' => $provider ['ort'],
            'Logo' => $provider ['media']      
          );                   
        }               
      }
      
      return $providers;
    }


    /**
     * liefert den vollständigen Kundendatensatz
     *
     * @param int $anbieterID Anbieter-ID
     * @return array
     */
    public function getAdress ($anbieterID)
    {            
      $model = $this->model('AnbieterData', $anbieterID);
      $provider_data = $model->getAnbieter ();
      
      $provider = array ();
      if (!empty ($provider_data))
      {        
        $provider ['Premium'] = $provider_data ['premium'];
        $provider ['Name 1'] = $provider_data ['firmenname'];
        $provider ['Name 2'] = '';
        $provider ['Name 3'] = '';
        $provider ['Name 4'] = '';
        $provider ['Land'] = $provider_data ['land'];
        $provider ['PLZ'] = $provider_data ['plz'];
        $provider ['Ort'] = $provider_data ['ort'];
        $provider ['Straße'] = $provider_data ['strasse'];
        $provider ['Hausnummer'] = $provider_data ['hausnummer'];
        $provider ['Telefon'] = $provider_data ['fon'];
        $provider ['Telefax'] = $provider_data ['fax'];
        $provider ['E-Mail'] = $provider_data ['email'];
        $provider ['Internetadresse'] = $provider_data ['www'];
        if (!empty ($provider_data ['media']))
          $provider ['Logo'] = $provider_data ['media'];               
      }
      
      return $provider;
    }

   /**
     * liefert den vollständigen Kundendatensatz
     *
     * @param int $anbieterID Anbieter-ID
     * @param int $systemID System-ID
     * @return array
     */
    public function getAdress2 ($anbieterID, $systemID)
    {
      return $this->getAdress ($anbieterID);
    }
	
    /**
     * liefert das Produktspektrum einer Firma
     *
     * @param int $systemID System-ID
     * @param int $anbieterID Anbieter-ID
     * @return array
     */
    public function getProduktSpektrum4Firma ($systemID, $anbieterID)
    {      
      return $this->getProdukSpektrum ($systemID, $anbieterID);
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
        $this->handleWSDL ();
      else
        $this->handleSOAP ();      
    }

    /**
     * initiiert den SAOP-WSDL-Handler
     *
     * @return void
     */
    private function handleWSDL ()
    {
      $params = $this->_request->getParams ();
      $autodiscover = new Zend_Soap_AutoDiscover ();      
      $autodiscover->setClass ('wcosWebservice');                  
      $autodiscover->setObject (new wcosWebservice ($params ['system']));            
      $autodiscover->handle ();
    }

    /**
     * initiiert den SOAP-Handler ohne WSDL
     *
     * @return void
     */
    private function handleSOAP ()
    {
      $params = $this->_request->getParams ();
      $soap = new Zend_Soap_Server (null, array('uri' => 'http://172.28.21.1/zbvs/public/soap/index'));            
      $soap->setClass ('wcosWebservice');                        
      $soap->setObject (new wcosWebservice ($params ['system']));      
      $soap->handle ();
      //error_log (print_r ($soap->getLastResponse(),true), 3, '/home/daniel/www/wcos2/debug_log.log');
    }
  }

?>
