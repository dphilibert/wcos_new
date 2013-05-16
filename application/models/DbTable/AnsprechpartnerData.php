<?php

  /**
   * Ansprechpartner-Model
   *  
   */
  class Model_DbTable_AnsprechpartnerData extends Model_DbTable_Global
  {
           
    /**
     * Liefert eine Liste mit Ansprechpartnern
     *       
     * @param string $token Suchbegriff (token)     
     * @return array Ansprechpartner
     */
    public function get_contacts_list ($token = NULL)
    {      
      $select = $this->_db->select ()->from ('ansprechpartner')
              ->joinleft ('media', 'media.anbieterID = '.$this->provider_id.' AND media.system_id = '.$this->system_id.' AND media_type=4 AND object_id = ansprechpartner.id', array ('media'))
              ->where ('ansprechpartner.anbieterID = '. $this->provider_id)->where ('ansprechpartner.system_id = '. $this->system_id)->order ('ansprechpartner.id ASC');      
      if (!empty ($token)) 
        $select->where ("vorname LIKE '$token%' OR nachname LIKE '$token%'");      
                             
      return $this->_db->fetchAll ($select);
    }

    /**
     * Liefert einen Ansprechpartner-Datensatz
     *
     * @param int $apID ansprechpartnerID
     * @return array
     *
     */
    public function get_contact ($apID)
    {      
      $select = $this->_db->select ()->from ('ansprechpartner')->where ('id = ?', $apID);     
      return $this->_db->fetchRow ($select);
    }

    /**
     * Ansprechpartner hinzfuegen
     * 
     * @param array $params Formularparameter
     * @return void
     */
    public function add_contact ($params)
    {            
      $filename = (!empty ($params ['file_name'])) ? $params ['file_name'] : '';
      unset ($params ['file_name'], $params ['file_name_orig']);      
      $this->_db->insert ('ansprechpartner', $params);                  
      if (!empty ($filename))
        $this->new_media ($filename, 4, $this->_db->lastInsertId ());                      
    }        

    /**
     * Ansprechpartner aktualisieren
     * 
     * @param array $params
     * @return void 
     */
    public function update_contact ($params)
    {                        
      if (!empty ($params ['file_name']))
        $this->new_media ($params ['file_name'], 4, $params ['id']);
      unset ($params ['file_name'], $params ['file_name_orig']);
      $this->_db->update ('ansprechpartner', $params, 'id = '. $params ['id']);
    } 
    
    /**
     * Ansprechpartner loeschen
     * 
     * @param int $id Ansprechpartner-ID
     * @return void 
     */
    public function delete_contact ($id)
    {
      $this->_db->delete ('ansprechpartner', 'id = '. $id);
      $this->_db->delete ('media', 'media_type=4 AND object_id = '. $id);
    }  
    
    /**
     * Uebernimmt die Ansprechpartner aus einem anderen System
     *      
     * @param int $from_system Quellsystem
     * @return void
     */
    public function copy_contacts ($from_system)
    {
      $query = $this->_db->select ()->from ('ansprechpartner')->where ('anbieterID = '. $this->provider_id)->where ('system_id = '. $from_system);     
      $contacts = $this->_db->fetchAll ($query);
                  
      if (!empty ($contacts))
      {  
        $this->_db->delete ('ansprechpartner', 'anbieterID = '.$this->provider_id.' AND system_id = '. $this->system_id);      
        foreach ($contacts as $contact)
        {
          $contact ['system_id'] = $this->system_id;
          unset ($contact ['id']);
          $this->_db->insert ('ansprechpartner', $contact);
        }
      }  
    }        
    
  }

