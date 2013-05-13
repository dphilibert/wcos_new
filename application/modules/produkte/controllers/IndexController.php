<?php

  /**
   * Produkte
   *    
   */
  class Produkte_IndexController extends Zend_Controller_Action
  {

    /**
     * Model
     * @var object 
     */
    var $model;
    
    /**
     * Parameter
     * @var array 
     */
    var $params;
    
    /**
     * Initialisierung - Ajax-Context und Model
     *  
     */
    public function init ()
    {
      $this->params = $this->_request->getParams ();
      $action = $this->params ['action'];
      if ($action == 'add' OR $action == 'remove')
      {
        $this->_helper->_layout->disableLayout ();
        $this->_helper->viewRenderer->setNoRender (true);
      }        
      $this->model = new Model_DbTable_ProduktcodesData ();            
    }        
    
    /**
     * Produktbaum 
     *     
     */
    public function indexAction ()
    {
      $this->view->provider_products = $this->model->get_provider_product_tree ();       
      $this->view->products = $this->model->get_product_tree ();  
    }
    
    /**
     * Produkte zum Spektrum hinzufuegen
     *  
     */
    public function addAction ()
    {      
      $this->model->add_products ($this->params ['codes']);
      $this->model->history ();
      echo $this->view->Tree ($this->model->get_provider_product_tree (), true);
    }        
    
    /**
     * Produkte aus dem Spektrum entfernen
     *  
     */
    public function removeAction ()
    {      
      $this->model->remove_products ($this->params ['codes']);
      $this->model->history ();
      echo $this->view->Tree ($this->model->get_provider_product_tree (), true);
    }        
    
    /**
     * Uebernimmt das Produktspektrum aus einen anderen System
     *  
     */
    public function copyAction ()
    {      
      $this->model->copy_products ($this->params ['from_system']);
      $this->model->history ();
      $this->_redirect ('/produkte/index/index');
    }        
    
  }

?>
