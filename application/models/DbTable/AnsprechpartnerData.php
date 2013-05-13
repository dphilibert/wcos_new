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
              ->where ('anbieterID = '. $this->provider_id)->where ('system_id = '. $this->system_id)->order ('id ASC');      
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
      $this->_db->insert ('ansprechpartner', $params);
      $id = $this->_db->lastInsertId ();
                  
      if (!empty ($_FILES ['image']['name']))
        $this->upload_file ('image', 4, $id);                      
    }        

    /**
     * Ansprechpartner aktualisieren
     * 
     * @param array $params
     * @return void 
     */
    public function update_contact ($params)
    {            
      if (!empty ($_FILES ['image']['name']))
        $this->upload_file ('image', 4, $params ['id']);
      
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
      $this->_db->delete ('media', 'object_id = '. $id);
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

