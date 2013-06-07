<?php
  /**
   * Produkte-Model
   *   
   */
  class Model_DbTable_ProduktcodesData extends Model_DbTable_Global
  {
   
    /**
     * Holt das Anbieter-Produktspektrum
     *  
     * @return array Anbieter-Produktspektrum
     */
    public function get_provider_product_tree ()
    {
      $query = $this->_db->select ()->from ('product2provider', array ())->where ('anbieterID = '. $this->provider_id)
              ->join ('products', 'product2provider.product = products.code AND products.system_id ='.$this->system_id);  
      $branches = $this->_db->fetchAll ($query);
      
      $product_tree = array ();
      foreach ($branches as $branch)      
        $product_tree [$branch ['haupt']][$branch ['ober']][] = array ('name' => $branch ['name'], 'code' => $branch ['code']);
              
      return $product_tree;
    }        
    
    /**
     * Holt das gesamte Produktspektrum eines Systems
     * 
     * @return array Produktspektrum 
     */
    public function get_product_tree ()
    {
      $query = $this->_db->select ()->from ('products')->where ('system_id ='.$this->system_id);
      $branches = $this->_db->fetchAll ($query);
      
      $product_tree = array ();
      foreach ($branches as $branch)      
        $product_tree [$branch ['haupt']][$branch ['ober']][] = array ('name' => $branch ['name'], 'code' => $branch ['code']);
              
      return $product_tree;
    }        
    
    /**
     * Fuegt Produkte dem Anbieter-Produktspektrum hinzu
     * 
     * @param string $codes Produktcodes 
     * @return void
     */
    public function add_products ($codes)
    {      
      $products = array_filter (explode (',', $codes));
      $codes = implode (',', $products);      
      $query = $this->_db->select ()->from ('product2provider', 'product')->where ('anbieterID = '. $this->provider_id)
              ->where ('product IN('.$codes.')');          
      $allready_assigned = $this->_db->fetchCol ($query);      
      foreach ($products as $code)
      {  
        if (in_array ($code, $allready_assigned)) continue;
        $this->_db->insert ('product2provider', array ('product' => $code, 'anbieterID' => $this->provider_id, 'system_id' => $this->system_id));
      }          
    }        
    
    /**
     * Entfernt Produkte aus dem Anbieter-Produktspektrum
     * 
     * @param string $codes Produktcodes 
     * @return void
     */
    public function remove_products ($codes)
    {
      $products = array_filter (explode (',', $codes));
      $codes = implode (',', $products);
      $this->_db->delete ('product2provider', 'product IN('.$codes.') AND anbieterID = '.$this->provider_id);              
    }        
    
    /**
     * Ersetzt das Produktspektrum des aktuellen Anbieters durch das Produktspektrum des Imports 
     * 
     * @param void
     * @return void 
     */
    public function import_provider_products ()
    {                      
      $transfer = new Zend_File_Transfer_Adapter_Http ();
      $info = pathinfo ($transfer->getFileName ('import'));                                     
      $transfer->setDestination (UPLOAD_PATH);          
      $transfer->receive ('import');
                       
      $import_file = UPLOAD_PATH.$info ['filename'].'.'.$info ['extension'];                   
      if (file_exists ($import_file) AND strtolower ($info ['extension']) == 'txt')
      {                
        $handle = fopen ($import_file, 'r');
        $cleared = array ();
        while ($line = fgets ($handle))
        {                   
          $line = preg_replace ('/\\s/', ' ', $line);
          $data = array_filter (explode (" ", $line));          
          
          if (!in_array ($data [0], $cleared))
          {        
            if (is_numeric ($data [0]))
              $this->_db->delete ('product2provider', 'anbieterID = '. $data [0]);
            $cleared [] = $data [0];
          }
          
          if (is_numeric ($data [1]))
            $this->_db->insert ('product2provider', array ('product' => $data [1], 'anbieterID' => $data [0]));                    
        }
        
        fclose ($handle);        
      }
      
      if (file_exists ($import_file))
        unlink ($import_file);
    }
    
    /**
     * liefert den Namen zu einem Produkt-Code
     * 
     * @param int $code Produkt-Code
     * @return string Produkt-Name 
     */
    public function get_product_name ($code)
    {
      $query = $this->_db->select ()->from ('products', 'name')->where ('code = '.$code);
      return $this->_db->fetchOne ($query);
    }
    
    /**
     * liefert die Anzahl an Anbieter zu einem Produktcode
     * 
     * @param int $code Produkt-Code
     * @return int Anzahl Anbieter 
     */
    public function count_providers ($code)
    {
      $query = $this->_db->select ()->from ('anbieter')->join ('systeme', 'systeme.anbieterID = anbieter.anbieterID AND systeme.system_id = '.$this->system_id, array ('premium'))
              ->join ('product2provider', 'anbieter.anbieterID = product2provider.anbieterID AND product = '.$code, array ());            
      $data = $this->_db->fetchAll ($query);
      return count ($data);
    }        
    
    /**
     * liefert die Anbieter zu einem Produkt-Code
     * 
     * @param int $code Produkt-Code
     * @return array Anbieter 
     */
    public function get_providers ($code)
    {
      $query = $this->_db->select ()->from ('anbieter')->join ('systeme', 'systeme.anbieterID = anbieter.anbieterID AND systeme.system_id = '.$this->system_id, array ('premium'))
              ->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.id')->join ('product2provider', 'anbieter.anbieterID = product2provider.anbieterID AND product = '.$code, array ());
      return $this->_db->fetchAll ($query);                      
    }        
    
  }

?>