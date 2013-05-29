<?php

/**
 * Cron-Model
 *  
 */
class Model_DbTable_Cron extends Zend_Db_Table_Abstract
{
  
  /**
   * setzt alle abgelaufenen Premium-Stati auf Standard
   * 
   * @param void
   * @return void 
   */
  public function cron_premium ()
  {
    $now = new Zend_Date (date ('d.m.Y'));    
    $this->_db->update ('systeme', array ('premium' => 0, 'start' => '', 'end' => ''), 'end <'.$now->get (Zend_Date::TIMESTAMP));
  }        
  
  /**
   * loescht alle abgelaufene Termine
   *  
   * @param void
   * @return void
   */
  public function cron_dates ()
  {
    $now = new Zend_Date (date ('d.m.Y'));
    $this->_db->delete ('termine', 'ende <'.$now->get (Zend_Date::TIMESTAMP));
  }        
    
}

?>
