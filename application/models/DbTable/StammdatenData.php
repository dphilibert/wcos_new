<?php
  /**
   * Stammdaten-Model
   *   
   */
  class Model_DbTable_StammdatenData extends Model_DbTable_Global
  {            
    /**
     * liefert die Stammdaten eines Anbieters
     *
     * @param int $ID anbieterID
     *
     * @return mixed
     */
    public function get_address ()
    {      
      $query = $this->_db->select ()->from ('anbieter', array ('id', 'name1', 'name2'))->where ('anbieter.anbieterID = '. $this->provider_id)      
      ->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.id');                  
      return $this->_db->fetchRow ($query);
    }
    
    /**
     * Führt die Stammdatenaktualisierung anhand der Formularparameter durch
     * 
     * @param array $params Formularparameter
     * @return void 
     */
    public function update_address ($params)
    {                                    
      if (!empty ($_FILES ['logo']['name']))
        $this->upload_file ('logo', 5, $params ['id']);
                
      $provider_id = $params ['anbieterID'];      
      $root_id = $params ['id'];
      unset ($params ['anbieterID'], $params ['id']);         
      $provider_params = array ('name1' => $params ['name1'], 'name2' => $params ['name2']);
      unset ($params ['name1'], $params ['name2']);
      
      $this->_db->update ('anbieter', $provider_params, 'anbieterID = '. $provider_id);
      $this->_db->update ('stammdaten', $params, 'id = '. $root_id);           
    } 
    
     
  }

?>
