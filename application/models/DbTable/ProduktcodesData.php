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
              ->where ('product2provider.systems LIKE "%'.$this->system_id.'%"')->join ('products', 'product2provider.product = products.code AND products.systems LIKE "%'.$this->system_id.'%"');  
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
      $query = $this->_db->select ()->from ('products')->where ('systems LIKE "%'.$this->system_id.'%"');
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
              ->where ('systems LIKE "%'.$this->system_id.'%"')->where ('product IN('.$codes.')');          
      $allready_assigned = $this->_db->fetchCol ($query);      
      foreach ($products as $code)
      {  
        if (in_array ($code, $allready_assigned)) continue;
        $this->_db->insert ('product2provider', array ('product' => $code, 'anbieterID' => $this->provider_id, 'systems' => $this->system_id));
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
      $this->_db->delete ('product2provider', 'product IN('.$codes.') AND anbieterID = '.$this->provider_id.' AND systems LIKE "%'.$this->system_id.'%"');              
    }        
    
    /**
     * Übernimmt das Produktspektrum aus einem anderen System
     * 
     * @param int $from_system Quellsystem
     * @return void 
     */
    public function copy_products ($from_system)
    {
      $query = $this->_db->select ()->from ('product2provider')->where ('anbieterID = '. $this->provider_id)->where ('systems LIKE "%'.$from_system.'%"');
      $codes = $this->_db->fetchAll ($query);
            
      if (!empty ($codes))
      {  
        //muss für das zielsystem erlaubt sein
        foreach ($codes as $key => $code)
        {
          $query = $this->_db->select()->from ('products')->where ('code = '.$code ['product'])->where ('systems LIKE "%'.$this->system_id.'%"');
          $check = $this->_db->fetchRow ($query);
          if (empty ($check)) unset ($codes [$key]);          
        }          
        if (!empty ($codes))
        {  
          $this->_db->delete ('product2provider', 'anbieterID = '.$this->provider_id.' AND systems LIKE "%'.$this->system_id.'%"');      
          foreach ($codes as $code)
          {                    
            $code ['systems'] = $this->system_id;          
            $this->_db->insert ('product2provider', $code);
          }
        }        
      } 
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