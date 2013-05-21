<?php

  /**
   * Termine-Model
   *   
   */
  class Model_DbTable_TermineData extends Model_DbTable_Global
  {         
    /**
     * liefert die Terminliste fuer die Systeme
     *     
     * @param string $search_term Suchbegriff
     * @return array Termine
     */
    public function get_dates_list ($search_term = '')
    {      
      $query = $this->_db->select ()->from ('termine')
              ->joinleft ('media', 'media.anbieterID = '.$this->provider_id.' AND media.system_id = '.$this->system_id.' AND media_type=7 AND object_id = termine.id', array ('media', 'media_id' => 'media.id'))              
              ->where ('termine.anbieterID = '. $this->provider_id)->where ('termine.system_id = '. $this->system_id)->order ('termine.id ASC');
      if (!empty ($search_term))
        $query->where ('title LIKE "%'.$search_term.'%" OR ort LIKE "%'.$search_term. '%"');
      
      return $this->_db->fetchAll ($query);
    }

    /**
     * liefert einen Termin
     *
     * @param int $date_id Termin-ID
     * @return array Termin
     */
    public function get_date ($date_id)
    {      
      $query = $this->_db->select ()->from ('termine')->where ('id = '. $date_id);
      return $this->_db->fetchRow ($query);
    }    
    
    /**
     * Termin hinzufuegen
     * 
     * @param array $params Formularparameter
     * @return void 
     */
    public function add_date ($params)
    {
      $filename = (!empty ($params ['file_name'])) ? $params ['file_name'] : '';
      unset ($params ['file_name'], $params ['file_name_orig']);      
      $this->_db->insert ('termine', $params);
      if (!empty ($filename))
        $this->new_media ($filename, 7, $this->_db->lastInsertId ());             
    }        
    
    /**
     * Termin bearbeiten
     * 
     * @param array $params Formularparameter
     * @return void 
     */
    public function edit_date ($params)
    {
      if (!empty ($params ['file_name']))
        $this->new_media ($params ['file_name'], 7, $params ['id']);
      unset ($params ['file_name'], $params ['file_name_orig']);
      $this->_db->update ('termine', $params, 'id = '. $params ['id']);  
    }        
    
    /**
     * Termin loeschen
     * 
     * @param int $date_id Termin-ID
     * @return void 
     */
    public function delete_date ($date_id)
    {
      $this->_db->delete ('termine', 'id = '. $date_id);
    }        
    
    /**
     * Termine aus einem anderen System uebernehmen
     *     
     * @param int $from_system Quellsystem
     * @return void
     */
    public function copy_dates ($from_system)
    {
      $query = $this->_db->select ()->from ('termine')->where ('anbieterID = '. $this->provider_id)->where ('system_id = '. $from_system);
      $dates = $this->_db->fetchAll ($query);
      
      if (!empty ($dates))
      {  
        $this->_db->delete ('termine', 'anbieterID = '.$this->provider_id.' AND system_id = '. $this->system_id);      
        foreach ($dates as $date)
        {
          $date ['system_id'] = $this->system_id;
          unset ($date ['id']);
          $this->_db->insert ('termine', $date);
        }
      }  
    }        
    
  }

?>
