<?php

/**
 * Benutzderdefinierter Validator für die Anbieter-Formulare im Admin-Bereich 
 * die anbieterID und LebenszeitID muessen eindeutig sein 
 * 
 */
class ProviderValidator extends Zend_Validate_Abstract
{
  const NOT_UNIQUE = 'notUnique';
  const IDENTICAL = 'identical';
  
  protected $_messageTemplates = array (
    self::NOT_UNIQUE => 'Wert nicht eindeutig - bereits vorhanden',
    self::IDENTICAL => 'Lebenszeit-ID und Kundennummer identisch'  
  );
  
  /**
   * Fuehrt die Ueberpruefung durch
   * 
   * @param string $value Parameter Formularelement
   * @param type $context restliche Formularparameter
   * @return bool true wenn ok, false wenn nicht eindeutig
   */
  public function isValid ($value, $context = null)
  {
    if ($context ['anbieterID'] == $value AND $context ['LebenszeitID'] == $value)
    {
      $this->_error (self::IDENTICAL);
      return false;
    }
      
    $db = Zend_Registry::get ('db');
    $error = false;
    $column = ($context ['anbieterID'] == $value) ? 'anbieterID' : 'LebenszeitID' ;      
            
    //Tabelle anbieter
    $query = $db->select ()->from ('anbieter', $column)->where ($column.' = '.$value);
    if (!empty ($context ['id']))
      $query->where ('id !='. $context ['id']);
    $check = $db->fetchOne ($query);
    if (!empty ($check))
      $error = true;
     
    //Tabelle stammdaten        
    if ($column == 'LebenszeitID')
    {      
      $query = $db->select ()->from ('stammdaten', 'stammdatenID')->where ('stammdatenID = '. $value);
      if (!empty ($context ['id']))
      {
        $lifetime_id_now = $db->fetchOne ($db->select ()->from ('anbieter', 'LebenszeitID')->where ('id = '. $context ['id']));
        $query->where ('stammdatenID !='. $lifetime_id_now);
      }          
        
      $check = $db->fetchOne ($query);
      if (!empty ($check))
        $error = true;
    }  
    
    if ($error)
    {  
      $this->_error (self::NOT_UNIQUE);
      return false;
    } else
    {
      return true;
    }
  }            
}
?>
