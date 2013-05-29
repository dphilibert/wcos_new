<?php

/**
 * Cron-Jobs
 *  
 */
class Cron_JobController extends Zend_Controller_Action
{
  /**
   * Cron-Model
   * @var object 
   */
  var $model;
  
  /**
   * Initialisierung - kein View/Layout
   *  
   */
  public function init ()
  {
    $this->_helper->_layout->disableLayout ();
    $this->_helper->viewRenderer->setNoRender (true);
    $this->model = new Model_DbTable_Cron ();
  }        
  
  /**
   * setzt alle abgelaufenen Premium-Stati auf Standard
   *  
   */
  public function premiumAction ()
  {
    $this->model->cron_premium ();
  }        
  
  /**
   * loescht alle abgelaufene Termine
   *  
   */
  public function datesAction ()
  {
    $this->model->cron_dates ();  
  }        
    
}
?>
