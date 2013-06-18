<?php

/**
 * Cron-Model
 *  
 */
class Model_DbTable_Cron extends Zend_Db_Table_Abstract
{
  
  /**
   * setzt alle abgelaufenen Premium-Stati auf Standard
   * 
   * @param void
   * @return void 
   */
  public function cron_premium ()
  {
    $now = new Zend_Date (date ('d.m.Y'));    
    $this->_db->update ('systeme', array ('premium' => 0, 'start' => '', 'end' => ''), 'end <'.$now->get (Zend_Date::TIMESTAMP));
  }        
  
  /**
   * loescht alle abgelaufene Termine
   *  
   * @param void
   * @return void
   */
  public function cron_dates ()
  {
    $now = new Zend_Date (date ('d.m.Y'));
    $this->_db->delete ('termine', 'ende <'.$now->get (Zend_Date::TIMESTAMP));
  }        
    
  /**
   * Methoden fuer den Import der WCOS-DB in die WCOS2-DB
   * 
   */    
  public function provider_import ()
  {
    $import = $this->get_import_db_adapter ();
    $wcos1_upload_path = APPLICATION_PATH . '/../public/wcos1_uploads/';
    
    $query = $import->select ()->from ('anbieter', array ('anbieterID', 'stammdatenID', 'firmenname', 'name1', 'name2', 'anbieterhash', 'last_login', 'number', 'LebenszeitID', 'Suchname', 'lastChange', 'created', 'premiumLevel', 'systems'));    
    $anbieter_data = $import->fetchAll ($query);
    
    foreach ($anbieter_data as $anbieter)
    {            
      //visits
      $query = $import->select ()->from ('stats_visits', array ('COUNT(vmKundennummer)'))->where ('vmKundennummer ='.$anbieter ['anbieterID']);
      $visits = $import->fetchOne ($query);
      
      //Anbieter-Kern-Daten
      $anbieter_table_data = $anbieter;
      $premiumLevel = $anbieter_table_data ['premiumLevel'];
      $systems = $anbieter_table_data ['systems'];
      unset ($anbieter_table_data ['premiumLevel'], $anbieter_table_data ['systems']);
      $anbieter_table_data ['visits'] = $visits;
      $this->_db->insert ('anbieter', $anbieter_table_data);
      
      //Stammdaten
      $query = $import->select ()->from ('stammdaten', array ('stammdatenID', 'anbieterID', 'strasse', 'hausnummer', 'land', 'plz', 'ort', 'fon', 'fax', 'email', 'www', 'mediaID'))->where ('stammdatenID = '.$anbieter ['stammdatenID'])->where ('anbieterID ='.$anbieter ['anbieterID']);
      $stammdaten_table_data = $import->fetchRow ($query);
      //$mediaID = $stammdaten_table_data ['mediaID'];
      $stammdaten_table_data ['id'] = $stammdaten_table_data ['stammdatenID'];
      unset ($stammdaten_table_data ['mediaID'], $stammdaten_table_data ['stammdatenID']);
                            
      if (!empty ($stammdaten_table_data ['id']))                      
        $this->_db->insert ('stammdaten', $stammdaten_table_data);
            
      //Firmenlogo
      $query = $import->select ()->from ('media')->where ('anbieterID = '. $anbieter ['anbieterID'])->where ('mediatyp = "FIRMENLOGO"');
      $media_orig_data = $import->fetchRow ($query);
      if (!empty ($media_orig_data))
      {          
        $filename_new = md5 ($media_orig_data ['mediadatei'].rand().time ()).'.'.$media_orig_data ['mediaExtension'];
        shell_exec ('cp '.$wcos1_upload_path.$media_orig_data ['mediaID'].'.'.$media_orig_data ['mediaExtension'].' '.UPLOAD_PATH.$filename_new);      
        $this->_db->insert ('media', array ('anbieterID' => $media_orig_data ['anbieterID'], 'media_type' => 5, 'beschreibung' => $media_orig_data ['beschreibung'], 'media' => $filename_new, 'link' => $media_orig_data ['link'], 'object_id' => $anbieter ['stammdatenID'], 'system_id' => 0));
      }
      
      //System-Stati
      $system_data = array ();
      $system_data ['anbieterID'] = $anbieter ['anbieterID'];                  
                  
      $systeme =  array (1, 2, 3, 405, 999);        
      $premium_systems = array_filter (explode (',', $systems));
      $query = $import->select ()->from ('laufzeiten')->where ('anbieterID = '. $anbieter ['anbieterID']);
      $runtimes = $import->fetchRow ($query);
      foreach ($systeme as $system_id)
      {
        $system_data ['premium'] =  ($premiumLevel == 1 AND in_array ($system_id, $premium_systems)) ? 1 : 0;        
        $system_data ['start'] = '';
        $system_data ['end'] = '';
        $system_data ['system_id'] = $system_id;        
       
        if ($system_data ['premium'] == 1 AND !empty ($runtimes))
        {
          $start = new Zend_Date ($runtimes ['startdatum']);
          $system_data ['start'] = $start->get (Zend_Date::TIMESTAMP);
          $start_date_parts = array_filter (explode ('-', substr ($runtimes ['startdatum'], 0, 10)));
          $system_data ['end'] = mktime (0, 0, 0, (int)$start_date_parts [1] + $runtimes ['laufzeit'], (int)$start_date_parts [2], (int)$start_date_parts [0]);
        }  
        
        $this->_db->insert ('systeme', $system_data);  
      }                          
    }  
  }
  
  public function products_import ()
  {
    $import = $this->get_import_db_adapter ();
    
    //Produktspektrum
    $query = $import->select ()->from ('vm_produktcodes');
    $products_data = $import->fetchAll ($query);    
    foreach ($products_data as $product)
      $this->_db->insert ('products', array ('haupt' => $product ['hauptbegriff'], 'ober' => $product ['oberbegriff'], 'name' => $product ['branchenname'], 'code' => $product ['branchenname_nummer'], 'system_id' => $product ['systems']));  
    
    //Anbieter-Produktspektrum
    $query = $import->select ()->from ('vm_produktcode2kdnummer', array ('produktcode', 'vmKundennummer'));
    $provider_products_data = $import->fetchAll ($query);
           
    foreach ($provider_products_data as $provider_product)
    {
      if (!empty ($provider_product ['produktcode']) AND !empty ($provider_product ['vmKundennummer']))
        $this->_db->insert ('product2provider', array ('product' => $provider_product ['produktcode'], 'anbieterID' => $provider_product ['vmKundennummer']));  
    }
  }        

  public function profiles_import ()
  {
    $import = $this->get_import_db_adapter ();
    
    //Firmenprofile
    $query = $import->select ()->from ('firmenportraits');
    $firmenportraet_data = $import->fetchAll ($query);
    
    foreach ($firmenportraet_data as $portraets)
    {
      $anbieterID = $portraets ['anbieterID'];
      $data = $portraets;
      unset ($data ['firmenportraitID'], $data ['anbieterID']);
      foreach ($data as $type => $value)
      {          
        switch ($type)
        {      
          case 'firmenbeschreibung' : $type_id = 0; break;
          case 'produkte': $type_id = 1; break;
          case 'standorte': $type_id = 2; break;
          case 'firmenausrichtung': $type_id = 3; break;
          case 'qualitaetsmanagement': $type_id = 4; break;
          case 'dienstleistungen': $type_id = 5; break;
          case 'gruendungsjahr': $type_id = 6; break;
          case 'praesenz': $type_id = 7; break;
          case 'mitarbeiter': $type_id = 8; break;
          case 'zielmaerkte': $type_id = 9; break;
          case 'umsatz': $type_id = 10; break;
        }  
        
        $systeme = $this->get_import_systems ($anbieterID);        
        foreach ($systeme as $system_id)     
        {
          if (!empty ($value))
            $this->_db->insert ('profiles', array ('anbieterID' => $anbieterID, 'value' => $value, 'type' => $type_id, 'system_id' => $system_id));  
        }
      }        
    }     
  }        
  
  public function contacts_import ()
  {
    $import = $this->get_import_db_adapter ();
    $wcos1_upload_path = APPLICATION_PATH . '/../public/wcos1_uploads/';
    
    //Ansprechpartner
    $query = $import->select ()->from ('ansprechpartner', array ('anbieterID', 'vorname', 'nachname', 'abteilung', 'position', 'telefon', 'telefax', 'email', 'mediaID'));
    $contacts_data = $import->fetchAll ($query);
    
    foreach ($contacts_data as $contact)
    {
      $mediaID = $contact ['mediaID'];
      $contact_table_data = $contact;
      unset ($contact_table_data ['mediaID']);
      
      if (!empty ($mediaID))
      {  
        $query = $import->select ()->from ('media')->where ('mediaID = '. $mediaID);
        $media_orig_data = $import->fetchRow ($query);
        if (!empty ($media_orig_data))
        {  
          $filename_new = md5 ($media_orig_data ['mediadatei'].rand().time ()).'.'.$media_orig_data ['mediaExtension'];
          shell_exec ('cp '.$wcos1_upload_path.$media_orig_data ['mediaID'].'.'.$media_orig_data ['mediaExtension'].' '.UPLOAD_PATH.$filename_new);      
        }
      }
      
      $systeme = $this->get_import_systems ($contact ['anbieterID']);        
      foreach ($systeme as $system_id)
      {
        $contact_table_data ['system_id'] = $system_id;
        $this->_db->insert ('ansprechpartner', $contact_table_data);      
        $object_id = $this->_db->lastInsertId ();

        //Ansprechpartner-Bilder
        if (!empty ($mediaID) AND !empty ($media_orig_data))        
          $this->_db->insert ('media', array ('anbieterID' => $media_orig_data ['anbieterID'], 'media_type' => 4, 'beschreibung' => $media_orig_data ['beschreibung'], 'media' => $filename_new, 'link' => $media_orig_data ['link'], 'object_id' => $object_id, 'system_id' => 0));              
      }      
    }      
  }        
  
  public function dates_import ()
  {
    $import = $this->get_import_db_adapter ();
    $wcos1_upload_path = APPLICATION_PATH . '/../public/wcos1_uploads/';
    
    //Termine
    $query = $import->select ()->from ('termine', array ('anbieterID', 'name', 'teaser', 'beschreibung', 'typID', 'beginn', 'ende', 'ort', 'mediaID'));
    $dates_data = $import->fetchAll ($query);
    
    foreach ($dates_data as $date)
    {
      $mediaID = $date ['mediaID'];
      $data = $date;
      $data ['title'] = $data ['name'];            
      unset ($data ['mediaID'], $data ['name']); 
      
      if (!empty ($data ['beginn']))
      {  
        $beginn_date_parts = array_filter (explode ('.', $data ['beginn']));                     
        $data ['beginn'] = mktime (0, 0, 0, (int)$beginn_date_parts [1], (int)$beginn_date_parts [0], (int)$beginn_date_parts [2]);
      }      
      if (!empty ($data ['ende']))
      {  
      $ende_date_parts = array_filter (explode ('.', $data ['ende']));
      $data ['ende'] = mktime (0, 0, 0, (int)$ende_date_parts [1], (int)$ende_date_parts [0], (int)$ende_date_parts [2]);
      }
      
      if (!empty ($mediaID))
      {
        $query = $import->select ()->from ('media')->where ('mediaID = '. $mediaID);
        $media_orig_data = $import->fetchRow ($query);  
        if (!empty ($media_orig_data))
        {  
          $filename_new = md5 ($media_orig_data ['mediadatei'].rand().time ()).'.'.$media_orig_data ['mediaExtension'];
          shell_exec ('cp '.$wcos1_upload_path.$media_orig_data ['mediaID'].'.'.$media_orig_data ['mediaExtension'].' '.UPLOAD_PATH.$filename_new);   
        }
      }
      
      $systeme = $this->get_import_systems ($date ['anbieterID']);        
      foreach ($systeme as $system_id)
      {
        $data ['system_id'] = $system_id;
        $this->_db->insert ('termine', $data);
        $object_id = $this->_db->lastInsertId ();
        
         //Termine-Bilder
        if (!empty ($mediaID) AND !empty ($media_orig_data))
          $this->_db->insert ('media', array ('anbieterID' => $media_orig_data ['anbieterID'], 'media_type' => 7, 'beschreibung' => $media_orig_data ['beschreibung'], 'media' => $filename_new, 'link' => $media_orig_data ['link'], 'object_id' => $object_id, 'system_id' => 0));  
      }                        
    }      
  }
  
  public function whitepaper_import ()
  {
    $import = $this->get_import_db_adapter ();
    
    //Whitepaper
    $query = $import->select ()->from ('whitepaper');
    $whitepaper_data = $import->fetchAll ($query);
    
    foreach ($whitepaper_data as $wp)
    {  
      $systeme = $this->get_import_systems ($wp ['whitepaper_anbieterID']);        
      foreach ($systeme as $system_id)
      {
        $this->_db->insert ('whitepaper', array ('anbieterID' => $wp ['whitepaper_anbieterID'], 'link' => $wp ['whitepaper_link'], 'beschreibung' => $wp ['whitepaper_beschreibung'], 'title' => $wp ['whitepaper_kategorie'], 'system_id' => $system_id));     
      }      
    }
  }        
  
  public function media_import ()
  {
    $import = $this->get_import_db_adapter ();
    $wcos1_upload_path = APPLICATION_PATH . '/../public/wcos1_uploads/';
    
    //Bilder
    $query = $import->select ()->from ('media')->where ('mediatyp = "BILD"');
    $image_data = $import->fetchAll ($query);
    
    foreach ($image_data as $image)
    {
      $filename_new = md5 ($image ['mediadatei'].rand ().time ()).'.'.$image ['mediaExtension'];
      shell_exec ('cp '.$wcos1_upload_path.$image ['mediaID'].'.'.$image ['mediaExtension'].' '.UPLOAD_PATH.$filename_new);  
      
      $systeme = $this->get_import_systems ($image ['anbieterID']);        
      foreach ($systeme as $system_id)
      {
        $this->_db->insert ('media', array ('anbieterID' => $image ['anbieterID'], 'media_type' => 1, 'beschreibung' => $image ['beschreibung'], 'media' => $filename_new, 'link' => $image ['link'], 'system_id' => $system_id));
        $object_id = $this->_db->lastInsertId ();
        $this->_db->update ('media', array ('object_id' => $object_id), 'id = '.$object_id);      
      }      
    }  
        
    //Videos
    $query = $import->select ()->from ('media')->where ('mediatyp = "VIDEO"');
    $video_data = $import->fetchAll ($query);
    
    foreach ($video_data as $video)
    {
      if (!empty ($video ['embed']))
      {  
        $filename_new = $video ['embed'];            
      } else
      {
        $filename_new = md5 ($video ['mediadatei'].rand ().time ()).'.'.$video ['mediaExtension'];
        shell_exec ('cp '.$wcos1_upload_path.$video ['mediaID'].'.'.$video ['mediaExtension'].' '.UPLOAD_PATH.$filename_new);
      }  
      
      $systeme = $this->get_import_systems ($video ['anbieterID']);        
      foreach ($systeme as $system_id)
      {
        $this->_db->insert ('media', array ('anbieterID' => $video ['anbieterID'], 'media_type' => 2, 'beschreibung' => $video ['beschreibung'], 'media' => $filename_new, 'link' => $video ['link'], 'system_id' => $system_id));
        $object_id = $this->_db->lastInsertId ();
        $this->_db->update ('media', array ('object_id' => $object_id), 'id = '.$object_id);
      }      
    }      
  }        
  
  private function get_import_systems ($anbieterID)
  {
    $query = $this->_db->select ()->from ('systeme', 'system_id')->where ('anbieterID ='. $anbieterID);
    return $this->_db->fetchCol ($query);
  }        
  
  private function get_import_db_adapter ()
  {            
    throw new Zend_Exception ('Import deaktiviert');
    
    $config = new Zend_Config_Ini ('../application/configs/application.ini', APPLICATION_ENV);    
    if (empty ($config->importdb))
      throw new Zend_Exception ('Kein Adapter fuer die zu importierende DB definiert');
    
    return Zend_Db::factory ($config->importdb);
  }        
}

?>
