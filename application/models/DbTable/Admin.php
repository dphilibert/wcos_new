<?php

/**
 * Model für den Admin-Bereich
 *  
 */
class Model_DbTable_Admin extends Zend_Db_Table_Abstract
{
  /**
   * Stellt die Zend_Paginator-Funktionalitaet zur Verfuegung
   * 
   * @param array $array Daten
   * @param int $page aktuelle Seite
   * @return \Zend_Paginator 
   */
  public function paging ($array, $page)
  {    
    $paginator = new Zend_Paginator (new Zend_Paginator_Adapter_Array ($array));
    
    $paginator->itemCountPerPage = 10; 
    $paginator->pageCount = round (count ($array) / $paginator->itemCountPerPage);    
    $paginator->current = $page;
    $paginator->setCurrentPageNumber ($page);
    
    return $paginator;
  }        
    
  /**
   * Gibt die Anbieter fuer die Select-Auswahl zurueck
   * 
   * @param void
   * @return array Anbieterauswahl-Optionen 
   */
  public function provider_selections ()
  {
    $query = $this->_db->select ()->from ('anbieter', array ('anbieterhash', 'firmenname'))->order ('firmenname ASC');
    return $this->_db->fetchPairs ($query);
  }  
  
  /**
   * Gibt die Anbieter mit Ihren Stammdaten anhand des 
   * Suchbegriffs zurueck - anbieterID oder Firmenname
   * 
   * @param string $search_term Suchbegriff Name/ID
   * @return array Suchergebnisse 
   */
  public function provider_selection_search ($search_term)
  {
    $query = $this->_db->select ()->from ('anbieter', array ('anbieterID', 'firmenname', 'anbieterhash'))
            ->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.stammdatenID', array ('strasse', 'hausnummer', 'plz', 'ort'))
            ->order ('firmenname ASC');    
    if (is_numeric ($search_term))
      $query->where ('anbieter.anbieterID LIKE "'.(int)$search_term.'%"');            
    else
      $query->where ('firmenname LIKE "'.$search_term.'%"');
          
    return $this->_db->fetchAll ($query);      
  }        
  
  
  
}
?>
