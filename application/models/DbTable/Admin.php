<?php

/**
 * Home/Admin-Model
 *  
 */
class Model_DbTable_Admin extends Model_DbTable_Global
{
      
  /**
   * Gibt die Anbieter fuer die Select-Auswahl zurueck
   * 
   * @param void|ID-Spalte
   * @return array Anbieterauswahl-Optionen 
   */
  public function provider_selections ($id_column = 'anbieterhash')
  {
    $query = $this->_db->select ()->from ('anbieter', array ($id_column, 'firmenname'))->order ('firmenname ASC');
    return $this->_db->fetchPairs ($query);
  }  
  
  /**
   * Gibt die Anbieter mit Ihren Stammdaten anhand des 
   * Suchbegriffs zurueck - anbieterID oder Firmenname
   * fuer die Anbieter-Auswahl
   * 
   * @param string $search_term Suchbegriff Name/ID
   * @return array Suchergebnisse 
   */
  public function provider_selection_search ($search_term = '')
  {
    $query = $this->_db->select ()->from ('anbieter', array ('anbieterID', 'firmenname', 'anbieterhash'))
            ->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.id', array ('strasse', 'hausnummer', 'plz', 'ort'))
            ->order ('firmenname ASC');    
    if (is_numeric ($search_term))
      $query->where ('anbieter.anbieterID LIKE "'.(int)$search_term.'%"');            
    else if (!empty ($search_term))
      $query->where ('firmenname LIKE "'.$search_term.'%"');
          
    return $this->_db->fetchAll ($query);      
  }        
        
  /**
   * Gibt die Vorschaulinks (standard/premium) fuer den Anbieter zurueck
   * 
   * @param void
   * @return array Vorschaulinks 
   */
  public function preview_links ()
  {
    $systems_config = new Zend_Config (require APPLICATION_PATH.'/configs/systems.php');                                    
    $preview_links = array ();
    foreach ($this->systems as $system_id => $brand)
    { 
      if (!in_array ($system_id, $this->allowed_systems))
        continue;              
      $url = str_replace ('PROVIDERID', $this->provider_id, $systems_config->provider_urls->get ($system_id));
      $preview_links [] = array (
        'name' => $brand,
        'standard' => $url,
        'premium' =>  ($system_id == 1) ? $url.'&premium_preview=1' : '',
     );
    }       
    return $preview_links;      
  }   
      
  /**
   * Setzt den Anbieter-Status fuer ein System auf Premium - mit Laufzeit
   * 
   * @param array $params Formularparameter
   * @return void
   */
  public function premium ($params)
  {      
    $this->system_check ($params ['system_id']);    
    $params ['premium'] = 1;      
    $start = new Zend_Date ($params ['start']);
    $params ['start'] = $start->get (Zend_Date::TIMESTAMP);    
    $end = new Zend_Date ($params ['end']);
    $params ['end'] = $end->get (Zend_Date::TIMESTAMP);    
    $this->_db->update ('systeme', $params, 'anbieterID = '.$this->provider_id.' AND system_id = '. $params ['system_id']);          
  }        
      
  /**
   * Setzt den Anbieter-Status fuer ein System auf Standard
   * 
   * @param int $provider_id Anbieter-ID
   * @return void 
   */
  public function nopremium ($system_id)
  {    
    $this->system_check ($system_id);    
    $this->_db->update ('systeme', array ('premium' => 0, 'start' => '', 'end' => ''), 'anbieterID = '.$this->provider_id.' AND system_id = '. $system_id);
  }
  
  /**
   * Deaktiviert den Anbieter-Status fuer ein System
   * 
   * @param type $system_id 
   * @return void
   */
  public function deactivate ($system_id)
  {
    $this->_db->delete ('systeme', 'anbieterID = '. $this->provider_id.' AND system_id = '. $system_id);
  }        
      
  /**
   * Holt den Premium-Laufzeit-Status fuer die Systeme
   *
   * @param void    
   * @return array Laufzeit-Data 
   */
  public function premium_status ()
  {                  
    $status_data = array ();
    foreach ($this->systems as $system_id => $brand)
    {
      if (!in_array ($system_id, $this->allowed_systems))
        continue;              
      $query = $this->_db->select ()->from ('systeme')->where ('anbieterID = '. $this->provider_id)->where ('system_id = '. $system_id);
      $data = $this->_db->fetchRow ($query);                   
      $status_data [$system_id]['system'] = $brand;      
      $status_data [$system_id]['premium'] = (empty ($data)) ? 2 : $data ['premium'];      
      if (!empty ($data ['end']))
      { 
        $start = new Zend_Date ($data ['start']);
        $status_data [$system_id]['start'] = $start->toString ('dd.MM.YYYY');
        $end = new Zend_Date ($data ['end']);
        $status_data [$system_id]['end'] = $end->toString ('dd.MM.YYYY');        
        $now = new Zend_Date (time ());
        $end = new Zend_Date ($data ['end']);    
        $status_data [$system_id]['laufzeit'] = ceil ($end->sub ($now)->toValue ()/60/60/24). ' Tage';                
      }         
    }           
    return $status_data;
  }        
        
  /**
   * Ueberprueft ob ein System fuer einen Anbieter deaktiviert ist
   * und aktiviert dieses
   *  
   * @param int $system_id System-ID
   * @return void
   */
  private function system_check ($system_id)
  {
    $query = $this->_db->select ()->from ('systeme')->where ('anbieterID = '. $this->provider_id)->where ('system_id = '. $system_id);
    $check = $this->_db->fetchRow ($query);
    if (empty ($check))
      $this->_db->insert ('systeme', array ('anbieterID' => $this->provider_id, 'system_id' => $system_id, 'premium' => 0));
  }        
  
}
?>
