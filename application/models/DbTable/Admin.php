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
  public function paging ($array, $page, $items_per_page = 10)
  {            
    $paginator = new Zend_Paginator (new Zend_Paginator_Adapter_Array ($array));
    
    $paginator->setItemCountPerPage ($items_per_page); 
    $paginator->pageCount = round (count ($array) / $items_per_page);    
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
    
  /**
   * Gibt die Anbieter mit Ihren Stammdaten anhand des 
   * Suchbegriffs zurueck - anbieterID oder Firmenname
   * fuer die Anbieter-Liste 
   * 
   * @param type $search_term
   * @return type 
   */
  public function provider_search ($search_term)
  {
    $query = $this->_db->select ()->from ('anbieter', array ('anbieterID', 'companyID', 'firmenname', 'premiumLevel'))
            ->joinleft ('stammdaten', 'anbieter.stammdatenID = stammdaten.stammdatenID', array ('ort'))
            ->where ('deleted = 0')->order ('firmenname ASC');
     if (is_numeric ($search_term))
      $query->where ('anbieter.anbieterID LIKE "'.(int)$search_term.'%"');            
    else
      $query->where ('firmenname LIKE "'.$search_term.'%"');
    
    return $this->_db->fetchAll ($query);
  }        
  
  /**
   * Gibt eine Anbieter-Liste zurueck
   * 
   * @param void
   * @return array Anbieter-Liste 
   */
  public function provider_list ()
  {
    $query = $this->_db->select ()->from ('anbieter', array ('anbieterID', 'companyID', 'firmenname', 'premiumLevel'))
            ->joinleft ('stammdaten', 'anbieter.stammdatenID = stammdaten.stammdatenID', array ('ort'))
            ->where ('deleted = 0')->order ('firmenname ASC');
    return $this->_db->fetchAll ($query);
  }      
  
  /**
   * Holt Meta- und Stammdaten eines Anbieters
   * 
   * @param int $provider_id Anbieter-ID
   * @return array Anbieter-Info 
   */
  public function provider_info ($provider_id)
  {
    $query = $this->_db->select ()->from ('anbieter')->join ('stammdaten', 'anbieter.stammdatenID = stammdaten.stammdatenID')
            ->where ('anbieter.anbieterID = '.$provider_id);
    return $this->_db->fetchRow ($query);
  }        

  /**
   * Legt einen neuen Anbieter an
   *  
   * @param array $params Formular-Parameter
   * @return void
   */
  public function provider_new ($params)
  {
   //Anbieter Meta
   $this->_db->insert ('anbieter', array (
       'anbieterID' => $params ['anbieterID'],
       'systems' => implode (',', $params ['systems']),
       'firmenname' => $params ['firmenname'],
       'anbieterhash' => md5 ($params ['firmenname']),
       'premiumLevel' => $params ['premiumLevel'],
       'number' => $params ['anbieterID'],
       'Suchname' => $params ['firmenname'],
       'created' => date ('Y-m-d H:i:s')
       )); 
   $provider_id = $this->_db->lastInsertId (); 
   
   unset ($params ['systems'], $params ['firmenname'], $params ['premiumLevel']);  
   $session = new Zend_Session_Namespace ();
   $params ['userID'] = $session->userData ['userID'];
   
   //Anbieter Stamm
   $this->_db->insert ('stammdaten', $params);
   $address_id = $this->_db->lastInsertId ();
   
   $this->_db->update ('anbieter', array ('stammdatenID' => $address_id, 'companyID' => $address_id), 'id = '. $provider_id);
  }        
  
  /**
   * Aktualisiert einen Anbieter
   * 
   * @param array $params Formular-Parameter
   * @return void
   */
  public function provider_update ($params)
  {
    //Anbieter Meta
    $query = $this->_db->select ()->from ('anbieter', 'stammdatenID')->where ('id = '. $params ['id']);
    $address_id = $this->_db->fetchOne ($query);
    
    $this->_db->update ('anbieter',  array (
       'anbieterID' => $params ['anbieterID'],
       'systems' => implode (',', $params ['systems']),
       'firmenname' => $params ['firmenname'],
       'anbieterhash' => md5 ($params ['firmenname']),
       'premiumLevel' => $params ['premiumLevel'],
       'number' => $params ['anbieterID'],
       'Suchname' => $params ['firmenname']),
        'id = '. $params ['id']);
    
    unset ($params ['systems'], $params ['firmenname'], $params ['premiumLevel'], $params ['id']);  
    $session = new Zend_Session_Namespace ();
    $params ['userID'] = $session->userData ['userID'];
    
    //Anbieter Stamm
    $this->_db->update ('stammdaten', $params, 'stammdatenID = '. $address_id);
  }        
  
  /**
   * Loescht einen Anbieter
   * 
   * @param int $provider_id Anbieter-ID
   * @return void
   */
  public function provider_delete ($provider_id)
  {    
    $this->_db->update ('anbieter', array ('deleted' => 1), 'anbieterID = '. $provider_id);
  }
          
  /**
   * Gibt eine Benutzer-Liste zurueck
   * 
   * @param void
   * @return array Benutzer-Liste 
   */
  public function user_list ()
  {
    $query = $this->_db->select ()->from ('user', array ('userID', 'username', 'userStatus'))
            ->join ('anbieter', 'user.primaryAnbieterID = anbieter.anbieterID', array ('firmenname', 'last_login'))->order ('userID ASC');
    return $this->_db->fetchAll ($query);
  }
  
  /**
   * Legt einen neuen Benutzer an
   * 
   * @param array $params Formular-Parameter
   * @return void 
   */
  public function user_new ($params)
  {
    $params ['password'] = md5 ($params ['password']);
    $params ['userHash'] = md5 ($params ['username']);
    $this->_db->insert ('user', $params);    
  }        
  
  /**
   * Loescht einen Benutzer
   * 
   * @param int $user_id Benutzer-ID
   * @return void 
   */
  public function user_delete ($user_id)
  {
    $this->_db->delete ('user', 'userID = '.$user_id);
  }
  
  /**
   * Holt die Benutzerdaten eines bestimmten Benutzers
   * 
   * @param int $user_id Benutzer-ID
   * @return array User-Info 
   */
  public function user_info ($user_id)
  {
    $query = $this->_db->select ()->from ('user', array ('userID', 'username', 'userStatus', 'primaryAnbieterID'))
            ->where ('userID = '. $user_id);
    return $this->_db->fetchRow ($query);
  }        
  
  /**
   * Veraendert die Daten eines bestimmten Benutzers
   * 
   * @param array $params Formular-Parameter
   * @return void 
   */
  public function user_update ($params)
  {
    $params ['userHash'] = md5 ($params ['username']); 
    if (!empty ($params ['password'])) $params ['password'] = md5 ($params ['password']);    
    $this->_db->update ('user', $params, 'userID = '.$params ['userID']);
  }        
  
  public function user_search ($search_term)
  {
    $query = $this->_db->select ()->from ('user', array ('userID', 'username', 'userStatus'))
            ->join ('anbieter', 'user.primaryAnbieterID = anbieter.anbieterID', array ('firmenname', 'last_login'))->order ('userID ASC');
    if (is_numeric ($search_term))
      $query->where ('userID LIKE "'.$search_term.'%"');
    else  
      $query->where ('username LIKE "'.$search_term.'%"');
      
    return $this->_db->fetchAll ($query);
  }        
}
?>
