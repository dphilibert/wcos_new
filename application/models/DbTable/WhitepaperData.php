<?php

/**
 * Whitepaper-Model
 *  
 */
class Model_DbTable_WhitepaperData extends Model_DbTable_Global
{    
  /**
   * liefer eine Liste mit Whitepapern zu einem System
   *   
   * @param int $search_term Suchbegriff
   * @return array Whitepaper
   */
  public function get_whitepaper_list ($search_term = '')
  {    
    $select = $this->_db->select()->from ('whitepaper')->where ('anbieterID = '. $this->provider_id)
            ->where ('system_id = '. $this->system_id)->order ('id ASC');
    if (!empty ($search_term))
      $select->where ('title LIKE "%'.$search_term.'%"');
          
    return $this->_db->fetchAll ($select);
  }

  /**
   * liefert einen Whitepaper-Datensatz
   *
   * @param id $whitepaperID whitepaperID
   * @return mixed
   */
  public function get_whitepaper ($whitepaper_id)
  {
    $query = $this->_db->select ()->from ('whitepaper')->where ('id = '. $whitepaper_id);
    return $this->_db->fetchRow ($query);
  }

  /**
   * Whitepaper hinzufuegen
   * 
   * @param array $params Formularparameter
   * @return void
   */
  public function add_whitepaper ($params)
  {
    $this->_db->insert ('whitepaper', $params);
  }        
  
  /**
   * Whitepaper bearbeiten
   * 
   * @param array $params Formularparameter
   * @return void 
   */
  public function edit_whitepaper ($params)
  {
    $this->_db->update ('whitepaper', $params, 'id = '. $params ['id']);
  }        
  
  /**
   * Whitepaper loeschen
   * 
   * @param int $whitepaper_id Whitepaper-ID
   * @return void 
   */
  public function delete_whitepaper ($whitepaper_id)
  {
    $this->_db->delete ('whitepaper', 'id = '.$whitepaper_id);
  }        
  
  /**
   * Whitepaper aus anderen System uebernehmen
   *    
   * @param int $from_system Quell-System
   * @return void
   */
  public function copy_whitepapers ($from_system)
  {
    $query = $this->_db->select ()->from ('whitepaper')->where ('anbieterID = '. $this->provider_id)->where ('system_id = '. $from_system);
    $whitepapers = $this->_db->fetchAll ($query);

    if (!empty ($whitepapers))
    {  
      $this->_db->delete ('whitepaper', 'anbieterID = '.$this->provider_id.' AND system_id = '. $this->system_id);      
      foreach ($whitepapers as $whitepaper)
      {
        $whitepaper ['system_id'] = $this->system_id;
        unset ($whitepaper ['id']);
        $this->_db->insert ('whitepaper', $whitepaper);
      }
    }  
  }          
  
}

