<?php

  /**
   * Anbieter-Model
   *
   **/
  class Model_DbTable_AnbieterData extends Model_DbTable_Global
  {    
    /**
     * Anbieter suchen anhand eines Suchbegriffs
     *
     * @param string $searchPhrase Suchbegriff
     * @return array
     */
    public function searchAnbieter ($searchPhrase)
    {
      $query = $this->_db->select ()->from ('anbieter')->join ('systeme', 'systeme.anbieterID = anbieter.anbieterID AND systeme.system_id = '.$this->system_id, array ('premium'))
              ->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.id')
              ->joinleft ('media', 'media.anbieterID = anbieter.anbieterID AND media.media_type = 5')->where ('anbieter.firmenname LIKE "%'.$searchPhrase.'%"')->order ('anbieter.firmenname ASC');
      $providers = $this->_db->fetchAll ($query);      
      return array ('hits' => $providers);      
    }

    /**
     * Anbieter suchen anhand eines Suchbegriffs
     * 
     * @param string $searchPhrase Suchbegriff
     * @return array 
     */
    public function searchAnbieterInAlphabet ($searchPhrase)
    {
      $query = $this->_db->select ()->from ('anbieter')->join ('systeme', 'systeme.anbieterID = anbieter.anbieterID AND systeme.system_id = '.$this->system_id, array ('premium'))
              ->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.id')
              ->joinleft ('media', 'media.anbieterID = anbieter.anbieterID AND media.media_type = 5')->where ('anbieter.firmenname LIKE "'.$searchPhrase.'%"')->order ('anbieter.firmenname ASC');
      $providers = $this->_db->fetchAll ($query);      
      return array ('hits' => $providers);      
    }
    
    /**
     * Liefert die Details zu einem Anbieter
     *
     * @param void
     * @return array
     */
    public function getAnbieterDetails ()
    {
      $query = $this->_db->select ()->from ('anbieter')
            ->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.id')->where ('anbieter.anbieterID = '. $this->provider_id);
      $details = $this->_db->fetchRow ($query);            
      if (!empty ($details))
      {
        $data = array ();
        foreach ($details as $key => $value)
          $data [strtoupper ($key)] = $value;
        $details = $data;
      }  
      return $details;      
    }

    /**
     * liefert die angegebene Zahl von zufälligen Anbietern
     *
     * @param int $count Anzahl
     * @return array
     */
    public function getAnbieterRandom ($count)
    {
      $query = $this->_db->select ()->from ('anbieter')->join ('systeme', 'systeme.anbieterID = anbieter.anbieterID AND systeme.system_id = '.$this->system_id)
           ->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.id')->where ('systeme.premium = 1')->order ('RAND()')->limit ($count);
      $data = $this->_db->fetchAll ($query);
      return array ('hits' => $data);      
    }

    /**
     * liefert den Anbieter-Datensatz zu einen Anbieter-Hashwert
     *
     * @param string $hash Anbieter-Hash
     * @return array
     */
    public function getAnbieterByHash ($hash)
    {
      $query = $this->_db->select ()->from ('anbieter')
            ->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.id')->where ('anbieter.anbieterhash = "'. $hash.'"');
      $data = $this->_db->fetchRow ($query);          
      return $data;
    }
    
    /**
     * liefert einen Anbieter-Datensatz zu einer Kd.Nr.
     *
     * @param int $number Nummer
     * @return array
     */
    public function getAnbieterByKundennummer ($number)
    {
       $query = $this->_db->select ()->from ('anbieter')
            ->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.id')->where ('anbieter.number = '. $number);
      $data = $this->_db->fetchRow ($query);   
      return $data;
    }

    /**
     * liefert die angegebene Anzahl zuletzt geänderter Anbieter
     *
     * @param int $count Anzahl
     * @return array
     */
    public function getLastChanged ($count)
    {
      $query = $this->_db->select ()->from ('anbieter')->join ('systeme', 'systeme.anbieterID = anbieter.anbieterID AND systeme.system_id = '.$this->system_id)
           ->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.id')->where ('systeme.premium = 1')->order ('anbieter.lastChange DESC')->limit ($count);
      $data = $this->_db->fetchAll ($query);           
      return $data;
    }

    /**
     * liefert die angegebene Anzahl neuester Anbieter
     *     
     * @param int $count Anzahl
     * @return array
     */
    public function getNewest ($count)
    {
       $query = $this->_db->select ()->from ('anbieter')->join ('systeme', 'systeme.anbieterID = anbieter.anbieterID AND systeme.system_id = '.$this->system_id)
           ->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.id')->where ('systeme.premium = 1')->order ('created DESC')->limit ($count);
      $data = $this->_db->fetchAll ($query);           
      return $data;
    }

    /**
     * liefert eine bestimmte Anzahl der meist gesehenen Anbieter
     *
     * @param int $count Anzahl
     * @return array
     */
    public function getMostSeen ($count)
    {
      $query = $this->_db->select ()->from ('anbieter')->join ('systeme', 'systeme.anbieterID = anbieter.anbieterID AND systeme.system_id = '.$this->system_id)
           ->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.id')->where ('systeme.premium = 1')->order ('visits DESC')->limit ($count);
      $data = $this->_db->fetchAll ($query);           
      return $data;    
    }  
    
    /**
     * Aktualisiert den letzten Login des Anbieters
     * 
     */
    public function logged_in ()
    {
      $this->_db->update ('anbieter', array ('last_login' => date ('d.m.Y H:i:S')), 'anbieterID = '. $this->provider_id);
    }        
    
    /**
     * erhoeht den Besuche Zaehler fuer einen Anbieter
     *  
     */
    public function riseVisitCounter ()
    {
      $this->_db->update ('anbieter', array ('visits' => 'visits + 1', 'anbieterID = '. $this->provider_id));
    }        
  }

?>
