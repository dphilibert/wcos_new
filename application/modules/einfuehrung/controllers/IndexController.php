<?php

  /**
   * Einfuehrung
   *     
   */
  class Einfuehrung_IndexController extends Zend_Controller_Action
  {
    /**
     * Formularparameter
     * @var array 
     */
    var $params;
    
    /**
     * Session
     * @var object 
     */
    var $session;
    
    /**
     * Model
     * @var object
     */
    var $model;
    
    /**
     * Initialisierung - Parameter, Model, Session und Ajax-Context
     * 
     */
    public function init ()
    {            
      $params = $this->_request->getParams ();      
      $action = $params ['action'];
      if (!empty($params ['_system_id'])) $params ['system_id'] = $params ['_system_id'];
      unset ($params ['module'], $params ['controller'], $params ['action'], $params ['submit'], $params ['_system_id']);             
      $this->params = $params;
                  
      if ($action == 'nopremium' OR $action == 'deactivate')
      {
        $this->_helper->_layout->disableLayout ();
        $this->_helper->viewRenderer->setNoRender (true);
      }  
      
      $this->model = new Model_DbTable_Admin ();
      $this->session = new Zend_Session_Namespace ();
    }        
    
    /**
     * Einführungs-Ansicht "Home"
     *     
     */
    public function indexAction ()
    {                                  
      $this->view->overview = $this->model->premium_status ();                                                          
      $this->view->user_status = $this->session->userData ['userStatus'];            
    }

    /**
     * Anbieter-Status fuer Medienmarke auf Premium setzten/bearbeiten 
     *  
    */
    public function premiumAction ()
    {            
      $this->model->premium ($this->params);
      $this->_redirect ('/einfuehrung/index/index');
    }        
            
    /**
     * Anbieter-Status fuer Medienmarke auf Standard setzen
     *  
     */
    public function nopremiumAction ()
    {           
      $this->model->nopremium ($this->params ['system_id']);      
    }        
      
    /**
     * Anbieter-Status fuer Medienmarke deaktivieren
     *  
     */
    public function deactivateAction ()
    {
      $this->model->deactivate ($this->params ['system_id']);
    }        
    
  }

?>
