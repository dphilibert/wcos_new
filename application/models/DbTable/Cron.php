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
    
    $query = $import->select ()->from ('anbieter', array ('anbieterID', 'stammdatenID', 'firmenname', 'name1', 'name2', 'anbieterhash', 'last_login', 'number', 'LebenszeitID', 'Suchname', 'lastChange', 'created', 'premiumLevel'));    
    $anbieter_data = $import->fetchAll ($query);
    
    foreach ($anbieter_data as $anbieter)
    {            
      //visits
      $query = $import->select ()->from ('stats_visits', array ('COUNT(vmKundennummer)'))->where ('vmKundennummer ='.$anbieter ['anbieterID']);
      $visits = $import->fetchOne ($query);
      
      //Anbieter-Kern-Daten
      $anbieter_table_data = $anbieter;
      $premiumLevel = $anbieter_table_data ['premiumLevel'];
      unset ($anbieter_table_data ['premiumLevel']);
      $anbieter_table_data ['visits'] = $visits;
      $this->_db->insert ('anbieter', $anbieter_table_data);
      
      //Stammdaten
      $query = $import->select ()->from ('stammdaten', array ('stammdatenID', 'anbieterID', 'strasse', 'hausnummer', 'land', 'plz', 'ort', 'fon', 'fax', 'email', 'www', 'mediaID'))->where ('stammdatenID = '.$anbieter ['stammdatenID']);
      $stammdaten_table_data = $import->fetchRow ($query);
      $mediaID = $stammdaten_table_data ['mediaID'];
      unset ($stammdaten_table_data ['mediaID']);
      $this->_db->insert ('stammdaten', $stammdaten_table_data);
      
      //Firmenlogo - todo: systembezug?
      if (!empty ($mediaID))
      {  
        $query = $import->select ()->from ('media')->where ('mediaID = '. $mediaID);
        $media_orig_data = $import->fetchRow ($query);      
        $filename_new = md5 ($media_orig_data ['mediadatei'].rand().time ()).'.'.$media_orig_data ['mediaExtension'];
        shell_exec ('cp '.$wcos1_upload_path.$media_orig_data ['mediaID'].'.'.$media_orig_data ['mediaExtension'].' '.UPLOAD_PATH.$filename_new);      
        $this->_db->insert ('media', array ('anbieterID' => $media_orig_data ['anbieterID'], 'media_type' => 5, 'beschreibung' => $media_orig_data ['beschreibung'], 'media' => $filename_new, 'link' => $media_orig_data ['link'], 'object_id' => $anbieter ['stammdatenID'], 'system_id' => $SYSTEM_ID));
      }
      
      //System-Stati todo: systembezug, laufzeiten?
      $system_data = array ();
      $system_data ['anbieterID'] = $anbieter ['anbieterID'];
      $system_data ['premium'] = $premiumLevel;
      //$system_data ['start'] = '';
      //$system_data ['end'] = '';
      $system_data ['system_id'] = $SYSTEM_ID;
      
      $this->_db->insert ('systeme', $system_data);            
    }  
  }
  
  public function products_import ()
  {
    $import = $this->get_import_db_adapter ();
    
    //Produktspektrum
    $query = $import->select ()->from ('vm_productcodes');
    $products_data = $import->fetchAll ($query);    
    foreach ($products_data as $product)
      $this->_db->insert ('products', array ('haupt' => $product ['hauptbegriff'], 'ober' => $product ['oberbegriff'], 'name' => $product ['branchenname'], 'code' => $product ['branchenname_nummer'], 'system_id' => $product ['systems']));  
    
    //Anbieter-Produktspektrum
    $query = $import->select ()->from ('vm_produktcode2kdnummer', array ('produktcode', 'vmKundennummer'));
    $provider_products_data = $import->fetchAll ($query);
    foreach ($provider_products_data as $provider_product)
      $this->_db->insert ('product2provider', array ('product' => $provider_product ['produktcode'], 'anbieterID' => $provider_product ['vmKundennummer']));  
  }        

  public function profiles_import ()
  {
    $import = $this->get_import_db_adapter ();
    
    //Firmenprofile: todo: systembezug, firmenbeschreibung?
    $query = $import->select ()->from ('firmenportraits');
    $firmenportraet_data = $import->fetchAll ($query);
    
    foreach ($firmenportraet_data as $portraets)
    {
      $anbieterID = $portraets ['anbieterID'];
      $data = $portraets;
      unset ($data ['firmenportraitID'], $data ['anbieterID']);
      foreach ($data as $type => $value)
      {
        if ($type == 'firmenbeschreibung') continue;        
        switch ($type)
        {          
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
        $this->_db->insert ('profiles', array ('anbieterID' => $anbieterID, 'value' => $value, 'type' => $type_id, 'system_id' => $SYSTEM_ID));
      }        
    }     
  }        
  
  public function contacts_import ()
  {
    $import = $this->get_import_db_adapter ();
    $wcos1_upload_path = APPLICATION_PATH . '/../public/wcos1_uploads/';
    
    //Ansprechpartner: todo: systembezug?
    $query = $import->select ()->from ('ansprechpartner', array ('anbieterID', 'vorname', 'nachname', 'abteilung', 'position', 'telefon', 'telefax', 'email', 'mediaID'));
    $contacts_data = $import->fetchAll ($query);
    
    foreach ($contacts_data as $contact)
    {
      $mediaID = $contact ['mediaID'];
      $contact_table_data = $contact;
      unset ($contact_table_data ['mediaID']);
      $contact_table_data ['system_id'] = $SYSTEM_ID;
      $this->_db->insert ('ansprechpartner', $contact_table_data);      
      $object_id = $this->_db->lastInsertId ();
      
      //Ansprechpartner-Bilder: todo: systembezug?
      if (!empty ($mediaID))
      {
        $query = $import->select ()->from ('media')->where ('mediaID = '. $mediaID);
        $media_orig_data = $import->fetchRow ($query);      
        $filename_new = md5 ($media_orig_data ['mediadatei'].rand().time ()).'.'.$media_orig_data ['mediaExtension'];
        shell_exec ('cp '.$wcos1_upload_path.$media_orig_data ['mediaID'].'.'.$media_orig_data ['mediaExtension'].' '.UPLOAD_PATH.$filename_new);      
        $this->_db->insert ('media', array ('anbieterID' => $media_orig_data ['anbieterID'], 'media_type' => 4, 'beschreibung' => $media_orig_data ['beschreibung'], 'media' => $filename_new, 'link' => $media_orig_data ['link'], 'object_id' => $object_id, 'system_id' => $SYSTEM_ID));
      }        
    }      
  }        
  
  public function dates_import ()
  {
    $import = $this->get_import_db_adapter ();
    $wcos1_upload_path = APPLICATION_PATH . '/../public/wcos1_uploads/';
    
    //Termine - todo: systembezug?
    $query = $import->select ()->from ('termine', array ('anbieterID', 'name', 'teaser', 'beschreibung', 'typID', 'beginn', 'ende', 'ort', 'mediaID'));
    $dates_data = $import->fetchAll ($query);
    
    foreach ($dates_data as $date)
    {
      $mediaID = $date ['mediaID'];
      $data = $date;
      $data ['title'] = $data ['name'];      
      $data ['system_id'] = $SYSTEM_ID;
      unset ($data ['mediaID'], $data ['name']);      
      $beginn = new Zend_Date ($data ['beginn']);
      $data ['beginn'] = $beginn->get (Zend_Date::TIMESTAMP);
      $ende = new Zend_Date ($data ['ende']);
      $data ['ende'] = $ende->get (Zend_Date::TIMESTAMP);
      $this->_db->insert ('termine', $data);
      $object_id = $this->_db->lastInsertId ();
      
      //Termine-Bilder - todo: systembezug?
       if (!empty ($mediaID))
      {
        $query = $import->select ()->from ('media')->where ('mediaID = '. $mediaID);
        $media_orig_data = $import->fetchRow ($query);      
        $filename_new = md5 ($media_orig_data ['mediadatei'].rand().time ()).'.'.$media_orig_data ['mediaExtension'];
        shell_exec ('cp '.$wcos1_upload_path.$media_orig_data ['mediaID'].'.'.$media_orig_data ['mediaExtension'].' '.UPLOAD_PATH.$filename_new);      
        $this->_db->insert ('media', array ('anbieterID' => $media_orig_data ['anbieterID'], 'media_type' => 7, 'beschreibung' => $media_orig_data ['beschreibung'], 'media' => $filename_new, 'link' => $media_orig_data ['link'], 'object_id' => $object_id, 'system_id' => $SYSTEM_ID));
      }            
    }      
  }
  
  public function whitepaper_import ()
  {
    $import = $this->get_import_db_adapter ();
    
    //Whitepaper - todo: systembezug?
    $query = $import->select ()->from ('whitepaper');
    $whitepaper_data = $import->fetchAll ($query);
    
    foreach ($whitepaper_data as $wp)
      $this->_db->insert ('whitepaper', array ('anbieterID' => $wp ['whitepaper_anbieterID'], 'link' => $wp ['whitepaper_link'], 'beschreibung' => $wp ['whitepaper_beschreibung'], 'title' => $wp ['whitepaper_kategorie'], 'system_id' => $SYSTEM_ID));         
  }        
  
  public function media_import ()
  {
    $import = $this->get_import_db_adapter ();
    $wcos1_upload_path = APPLICATION_PATH . '/../public/wcos1_uploads/';
    
    //Bilder - todo: systembezug?
    $query = $import->select ()->from ('media')->where ('mediatyp = "BILD"');
    $image_data = $import->fetchAll ($query);
    
    foreach ($image_data as $image)
    {
      $filename_new = md5 ($image ['mediadatei'].rand ().time ()).'.'.$image ['mediaExtension'];
      shell_exec ('cp '.$wcos1_upload_path.$image ['mediaID'].'.'.$image ['mediaExtension'].' '.UPLOAD_PATH.$filename_new);      
      $this->_db->insert ('media', array ('anbieterID' => $image ['anbieterID'], 'media_type' => 1, 'beschreibung' => $image ['beschreibung'], 'media' => $filename_new, 'link' => $image ['link'], 'system_id' => $SYSTEM_ID));
      $object_id = $this->_db->lastInsertId ();
      $this->_db->update ('media', array ('object_id' => $object_id), 'id = '.$object_id);      
    }  
        
    //Videos - todo: bestehende video-dateien, systembezug?
    $query = $import->select ()->from ('media')->where ('mediatyp = "VIDEO"')->where ('embed != ""');
    $video_data = $import->fetchAll ($query);
    
    foreach ($video_data as $video)
    {
      $filename_new = $video ['embed'];            
      $this->_db->insert ('media', array ('anbieterID' => $video ['anbieterID'], 'media_type' => 2, 'beschreibung' => $video ['beschreibung'], 'media' => $filename_new, 'link' => $video ['link'], 'system_id' => $SYSTEM_ID));
      $object_id = $this->_db->lastInsertId ();
      $this->_db->update ('media', array ('object_id' => $object_id), 'id = '.$object_id);      
    }      
  }        
  
  private function get_import_db_adapter ()
  {    
    throw new Zend_Exception ('Import noch nicht verfügbar');
    
    $config = new Zend_Config_Ini ('../application/configs/application.ini', APPLICATION_ENV);    
    if (empty ($config->importdb))
      throw new Zend_Exception ('Kein Adapter fuer die zu importierende DB definiert');
    
    return Zend_Db::factory ($config->importdb);
  }        
}

?>
