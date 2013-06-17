<?php

  /**
   * Datenbank-Model für das Firmenportrait
   *  
   */
  class Model_DbTable_FirmenportraitData extends Model_DbTable_Global
  {
            
    /**
     * Holt die Firmenportraits für das jeweilige System
     * 
     * @param int $system_id Anbieter-ID
     * @return array Firmenportraits 
     */
    public function get_profile_list ()
    {        
      $mod_config = new Zend_Config (require APPLICATION_PATH.'/configs/module.php');
      $types = $mod_config->profiles->toArray ();
                         
      $query = $this->_db->select ()->from ('profiles')->where ('anbieterID = '.$this->provider_id)->where ('system_id = '.$this->system_id)->order ('type ASC');      
      $data = $this->_db->fetchAll ($query);            
      
      $profiles = array ();     
      foreach ($data as $profile)        
        $profiles [$types [$profile ['type']]] = array ('text' => $profile ['value'], 'id' =>   $profile ['id']);                     
                                              
      return $profiles;              
    }        
   
    /**
     * Holt ein Portraet
     * 
     * @param int $pid Portraet-Id 
     */
    public function get_profile ($pid)
    {
      $query = $this->_db->select ()->from ('profiles')->where ('id = '. $pid);      
      return $this->_db->fetchRow ($query);
    }        
    
    /**
     * Legt ein neues Firmenprofil an
     * 
     * @param array $params Formularparameter
     * @return void 
     */
    public function add_profile ($params)
    {
      $this->_db->insert ('profiles', $params);      
    }        
    
    /**
     * Aktualisiert ein Profil
     * 
     * @param array $params Formularparameter
     * @return void 
     */
    public function update_profile ($params)
    {
      $this->_db->update ('profiles', $params, 'id = '. $params ['id']);
    }        
    
    /**
     * Loescht ein Profil
     * 
     * @param int $id Profil-ID
     * @return void 
     */
    public function delete_profile ($id)
    {
      $this->_db->delete ('profiles', 'id = '. $id);
    }  
    
    /**
     * Uebernimmt die Profile aus einem anderen System
     *      
     * @param int $from_system System, dessen Profile kopiert werden sollen
     * @return void
     */
    public function copy_profiles ($from_system)
    {            
      $query = $this->_db->select ()->from ('profiles')->where ('anbieterID = '. $this->provider_id)->where ('system_id = '. $from_system)->order ('type ASC');
      $profiles = $this->_db->fetchAll ($query);
      
      if (!empty ($profiles))
      {  
        $this->_db->delete ('profiles', 'anbieterID = '.$this->provider_id.' AND system_id = '. $this->system_id);      
        foreach ($profiles as $profile)
        {
          $profile ['system_id'] = $this->system_id;
          unset ($profile ['id']);
          $this->_db->insert ('profiles', $profile);
        }
      }
    }        
    
    
  }
    
?>
