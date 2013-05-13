<?php

class Profile_Validator extends Zend_Validate_Abstract
{
  const ALREADY_EXISTS = 'already_exists';
  
  protected $_messageTemplates = array (
    self::ALREADY_EXISTS => "Es existiert bereits ein Profil dieses Typs für diese Medienmarke"  
  );
  
  public function isValid ($value, $context = NULL)
  {
    $db = Zend_Db_Table_Abstract::getDefaultAdapter ();    
        
    $query = $db->select ()->from ('profiles')->where ('type = '. $value)
            ->where ('system_id = '. $context ['system_id'])->where ('anbieterID = '. $context ['anbieterID']);
    if (!empty ($context ['id']))
      $query->where ('id != '. $context ['id']);
      
    $check = $db->fetchAll ($query);    
    if (!empty ($check))
    {
      $this->_error (self::ALREADY_EXISTS);
      return false;
    } else
    {
      return true;  
    }          
  }        
}
?>
