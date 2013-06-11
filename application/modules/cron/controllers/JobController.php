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
    
  
  /**
   * Aktionen fuer den Import der WCOS-DB in die WCOS2-DB 
   */
  
  
  public function providerimportAction ()
  {
    $this->model->provider_import ();
  }        
  
  public function productsimportAction ()
  {
    $this->model->products_import ();
  }   
  
  public function profilesimportAction ()
  {
    $this->model->profiles_import ();
  }        
  
  public function contactsimportAction ()
  {
    $this->model->contacts_import ();
  }        
  
  public function datesimportAction ()
  {
    $this->model->dates_import ();
  }        
  
  public function whitepaperimportAction ()
  {
    $this->model->whitepaper_import ();
  }        
  
  public function mediaimportAction ()
  {
    $this->model->media_import ();
  }        
  
}
?>
