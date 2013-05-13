<?php
class Model_DbTable_Global extends Zend_Db_Table_Abstract
{
  /**
   * Anbieter-ID
   * @var int 
   */
  var $provider_id;
  
  /**
   * Systeme
   * @var array 
   */
  var $systems;
  
  /**
   * erlaubte Systeme
   * @var array 
   */
  var $allowed_systems;
  
  /**
   *
   * ausgewähltes System
   * @var int
   */
  var $system_id;
  
  /**
   * Initialisierung - Anbieter-ID, Systeme, erlaubte Systeme
   *  
   */
  public function init ()
  {
    $session = new Zend_Session_Namespace ();
    $this->provider_id = $session->anbieterData ['anbieterID'];
    $this->system_id = $session->system_id;
    $systems_config = new Zend_Config (require APPLICATION_PATH.'/configs/systems.php');
    $this->systems = $systems_config->brands->toArray ();    
    $this->allowed_systems = explode (',', $session->userData ['systems']);
  }
  
  /**
   * Stellt die Zend_Paginator-Funktionalitaet zur Verfuegung
   * 
   * @param array $array Daten
   * @param int $page aktuelle Seite
   * @return \Zend_Paginator 
   */
  public function paging ($array, $page, $items_per_page = 10)
  {            
    $paginator = new Zend_Paginator (new Zend_Paginator_Adapter_Array ($array));
    
    $paginator->setItemCountPerPage ($items_per_page); 
    $paginator->pageCount = round (count ($array) / $items_per_page);    
    $paginator->current = $page;
    $paginator->setCurrentPageNumber ($page);
    
    return $paginator;
  }        
  
  /**
   * Prueft und Empfaengt eine hochgeladene Datei -
   * und nimmt den entsprechenden DB-Eintrag vor TODO: da läuft was noch nicht rund
   * 
   * @param int $name Formular-Element-Name
   * @param int $type Media-Typ
   * @param int $object_id ID des zugehoerigen Daensatzes der anderen Tabelle
   * @return void
   */
  public function upload_file ($name, $type, $object_id)
  {    
    $transfer = new Zend_File_Transfer_Adapter_Http ();
    //$transfer->addValidator ('IsImage', false);        
    $transfer->setDestination (APPLICATION_PATH . '/../public/uploads/');
      
    $file = $transfer->getFileInfo ($name);
    $transfer->receive ($name);
    
    //if ($transfer->isValid ($name))
    //{                              
      $query = $this->_db->select ()->from ('media')->where ('anbieterID = '. $this->provider_id)
              ->where ('media_type ='. $type)->where ('object_id = '. $object_id);        
      $check = $this->_db->fetchRow ($query);
      if (empty ($check))           
        $this->_db->insert ('media', array ('anbieterID' => $this->provider_id, 'media_type' => $type, 'media' => $file ['name'], 'object_id' => $object_id));              
      else        
        $this->_db->update ('media', array ('media' => $file ['name']), 'id = '. $check ['id']);      
    //}         
  }        
  
  /**
   * Gibt die erlaubten System-Auswahlen zurueck
   *  
   * @param void
   * @return array erlaubte Systeme
   */
  public function system_selection ()
  {
    $systems = $this->systems;
    foreach ($systems as $system_id => $brand)
    {
      if (!in_array ($system_id, $this->allowed_systems))
        unset ($systems [$system_id]);      
    }  
    return $systems;
  }        
  
  /**
   * Gibt Label und ID eines Systems zurueck
   * 
   * @param int $id System-ID 
   */
  public function get_system ($id)
  {
    return array ('id' => $id, 'brand' => $this->systems [$id]);
  }        
  

}

?>