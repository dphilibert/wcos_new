<?php

  /**
   * Media-Model 
   *   
   */
  class Model_DbTable_MediaData extends Model_DbTable_Global
  {
           
    /**
     * Liefert die Medienliste zu einem System
     *     
     * @param int $typ Bilder oder Videos
     * @param string $search_term Suchbegriff
     *
     * @return array Medien
     */
    public function get_media_list ($type, $search_term = '')
    {
      $query = $this->_db->select ()->from ('media')->where ('anbieterID = '. $this->provider_id)
              ->where ('system_id = '. $this->system_id)->where ('media_type = '. $type)->order ('id ASC');                  
      if (!empty ($search_term))
        $query->where ('media LIKE "%'.$search_term.'%" OR beschreibung LIKE "%'.$search_term.'%"');
      
      return $this->_db->fetchAll ($query);
    }

    /**
     * Holt einen Medien-Datensatz eines bestimmten Typs fuer ein System
     *      
     * @param int $type Medien-Typ
     * @param int $object_id zugehoerige Datensatz-ID
     * @return array Medie
     */
    public function get_media ($type, $object_id)
    {
      $query = $this->_db->select ()->from ('media')->where ('anbieterID = '. $this->provider_id)
              ->where ('media_type = '. $type)->where ('system_id = '. $this->system_id)->where ('object_id = '. $object_id);
      
      return $this->_db->fetchRow ($query);
    }        

    /**
     * Holt einen Mediendatensatz anhand der ID
     * 
     * @param int $id Media-ID
     * @return array Medie 
     */
    public function get_media_row ($id)
    {
      $query = $this->_db->select ()->from ('media')->where ('id = '. $id);
      return $this->_db->fetchRow ($query);
    }        
    
    /**
     * Fuegt einen Medien-Datensatz hinzu
     * 
     * @param array $params Formularparameter 
     * @return void
     */
    public function add_media ($params)
    {
      $this->_db->insert ('media', $params);
      $id = $this->_db->lastInsertId ();
      $this->_db->update ('media', array ('object_id' => $id), 'id = '. $id);      
      
      if (!empty ($_FILES ['media_file']['name']))                      
        $this->upload_file ('media_file', $params ['media_type'], $id);                    
    }        
    
    /**
     * Aktualisiert einen Medien-Datensatz
     * 
     * @param array $params Formularparameter
     * @return void 
     */
    public function update_media ($params)
    {
      $this->_db->update ('media', $params, 'id = '. $params ['id']);
            
      if (!empty ($_FILES ['media_file']['name']))                      
        $this->upload_file ('media_file', $params ['media_type'], $params ['object_id']);                    
    } 
    
    /**
     * Loescht einen Mediendatensatz
     * 
     * @param int $media_id Media-ID 
     * @return void
     */
    public function delete_media ($media_id)
    {
      $this->_db->delete ('media', 'id = '.$media_id);
    }        
    
    /**
     * Uebernimmt die Medien aus einen anderen System
     *      
     * @param int $from_system Quell-System
     * @param int $type Bilder oder Videos 
     * @return void
     */
    public function copy_media ($from_system, $type)
    {
      $query = $this->_db->select ()->from ('media')->where ('anbieterID = '. $this->provider_id)
              ->where ('media_type = '. $type)->where ('system_id = '. $from_system);
      $multimedia = $this->_db->fetchAll ($query);
      
      if (!empty ($multimedia))
      {  
        $this->_db->delete ('media', 'anbieterID = '.$this->provider_id.' AND system_id = '. $this->system_id.' AND media_type = '. $type);      
        foreach ($multimedia as $media)
        {
          $media ['system_id'] = $this->system_id;
          unset ($media ['id']);
          $this->_db->insert ('media', $media);
        }
      }
    }        
    
    /**
     * Modifiziert das Media-Formular entsprechend den Anforderungen
     * 
     * @param object $form Zend_Form 
     * @param int $media_type Media-ID
     * @param string $action_type new oder edit
     * @return void
     */
    public function mod_media_form (&$form, $media_type, $action_type = 'new')
    {                            
      if ($action_type == 'edit')
      {
        $id = new Zend_Form_Element_Hidden ('id');
        $id->setDecorators ($form->decorators);
        $form->addElement ($id);        
      }        
      if ($action_type == 'edit' AND $media_type == 1)
      {
        $file = $form->getElement ('media_file');
        $file->setRequired (false);
      }        
      $button = $form->getElement ('submit');
      $button->setAttrib ('onclick', 'submit_form ("/media/index/'.$action_type.'/media_type/'. $media_type.'");');
    }        
    
  }

?>
