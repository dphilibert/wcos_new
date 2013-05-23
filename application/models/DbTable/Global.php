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
   * Initialisierung - Anbieter-ID, System-ID, Systeme, erlaubte Systeme und DB-Adapter
   *  
   */  
  public function __construct ($config = array ())
  {
    $session = new Zend_Session_Namespace ();
    $systems_config = new Zend_Config (require APPLICATION_PATH.'/configs/systems.php');
    $this->systems = $systems_config->brands->toArray ();    
    $this->allowed_systems = explode (',', $session->userData ['systems']);    
    $this->provider_id = (!empty ($config ['provider_id'])) ? $config ['provider_id'] : $session->anbieterData ['anbieterID'];
    $this->system_id = (!empty ($config ['system_id'])) ? $config ['system_id'] : $session->system_id;
    $this->_db = Zend_Registry::get ('db');
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
   * Empfaengt das hochgeladene Firmenlogo  
   * 
   * @param int $name Formular-Element-Name
   * @param int $type Media-Typ
   * @param int $object_id ID des zugehoerigen Daensatzes der anderen Tabelle
   * @return void
   */
  public function upload_file ($name, $type, $object_id)
  {    
    $transfer = new Zend_File_Transfer_Adapter_Http ();            
    $transfer->setDestination (APPLICATION_PATH . '/../public/uploads/');    
    $transfer->receive ($name);
    $file = $transfer->getFileInfo ($name);
    $file = $file [$name];
                            
    //todo: per md5 neuen dateinamen bauen - Zend_Filter_Rename oder so 
    $this->new_media ($file ['name'], $type, $object_id);                        
  }        
  
  /**
   * Nimmt einen neuen Eintrag in der Media-Tabelle vor
   * 
   * @param int $filename Dateiname - kann auch embedded-code sein
   * @param int $type Media-Typ
   * @param int $object_id ID des zugehoerigen Daensatzes der anderen Tabelle
   * @return void
   */
  public function new_media ($filename, $type, $object_id)
  {
    $query = $this->_db->select ()->from ('media')->where ('anbieterID = '. $this->provider_id)
            ->where ('media_type ='. $type)->where ('object_id = '. $object_id)->where ('system_id = '. $this->system_id);        
    $check = $this->_db->fetchRow ($query);
    if (empty ($check))           
      $this->_db->insert ('media', array ('anbieterID' => $this->provider_id, 'media_type' => $type, 'media' => $filename, 'object_id' => $object_id, 'system_id' => $this->system_id));              
    else        
      $this->_db->update ('media', array ('media' => $filename), 'id = '. $check ['id']);
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
  
  /**
   * Modifiziert das Formular fuer die Anforderung der Bearbeitungs-Aktion
   * 
   * @param object $form Zend_Form
   * @param string $module Modul 
   * @return void
   */
  public function mod_form_edit (&$form, $module)
  {
    $id = new Zend_Form_Element_Hidden ('id');          
    $form->addElement ($id);    
    $button = $form->getElement ('submit');    
    
    $additional = '';
    if ($module == 'firmenportrait')
      $additional = ', "value"';
    else if ($module == 'termine')
      $additional = ', "", true';
    
    $button->setAttrib ('onclick', 'submit_form ("/'.$module.'/index/edit"'.$additional.');');      
  }        

  /**
   * Nimmt einen Eintrag in der History-Tabelle vor
   * 
   * @param void
   * @return void
   */
  public function history ()
  {
    $session = new Zend_Session_Namespace ();    
    $params = Zend_Controller_Front::getInstance ()->getRequest ()->getParams ();      
    $object_id = 0;
    switch ($params ['module'])
    {
      case 'einfuehrung': $object_id = $params ['_system_id']; break;
      case 'stammdaten': $object_id = $params ['id']; break;
      case 'produkte': 
        if ($params ['action'] == 'add' OR $params ['action'] == 'remove') $object_id = $params ['codes'];  
        elseif ($params ['action'] == 'copy') $object_id = $params ['from_system'];        
      break;
      default:                 
        if ($params ['action'] == 'edit' OR $params ['action'] == 'delete') $object_id = $params ['id'];
        elseif ($params ['action'] == 'new') $object_id = $this->_db->lastInsertId ();
        elseif ($params ['action'] == 'copy') $object_id = $params ['from_system'];
      break;  
    }        
    $this->_db->insert ('history', 
      array ('user_id' => $session->userData ['user_id'], 'module' => $params ['module'], 'action' => $params ['action'],
        'anbieterID' => $this->provider_id, 'system_id' => $this->system_id, 'tstamp' => date ('d.m.Y - H:i:s'), 'object_id' => $object_id));
  }        
  
  /**
   * Ersetzt das Eingabefeld für den Upload durch den Infobereich mit dem Dateinamen des hochgeladenen Bilds
   * 
   * @param string $form Formular
   * @param string $file_name Dateiname
   * @return string Formular 
   */
  public function add_file_info ($form, $file_name_orig, $file_name)
  {  
    $file_name_orig = (strlen ($file_name_orig) > 27) ? substr ($file_name_orig, 0, 23).'...' : $file_name_orig;
    return str_replace ('<input type="file" name="image" id="image" onchange="upload (this);">', 
            '<div id="upload_info" class="alert alert-success" style="width:190px;padding-left:5px;padding-right:25px;"><b>'.$file_name_orig.'</b>
            <button type="button" class="close" style="font-size:17px;" onclick="remove_file (\''. $file_name.'\');">x</button></div>', $form);    
  }        
  
}

?>