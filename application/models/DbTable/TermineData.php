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
      $query = $this->_db->select ()->from ('termine')->where ('anbieterID = '. $this->provider_id)
            ->where ('system_id = '. $this->system_id)->order ('id ASC');
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
      $this->_db->insert ('termine', $params);
      
    }        
    
    /**
     * Termin bearbeiten
     * 
     * @param array $params Formularparameter
     * @return void 
     */
    public function edit_date ($params)
    {
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
