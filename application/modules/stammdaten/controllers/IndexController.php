<?php

  /**
   * Stammdaten
   *  
   */
  class Stammdaten_IndexController extends Zend_Controller_Action
  {   
    /**
     * Parameter
     * @var array 
     */
    var $params;
    
    /**
     * Stammdaten-Model
     * @var object 
     */
    var $model;
      
    /**
     * Initialisierung - Parameter, Model
     *  
     */
    public function init ()
    {
      $params = $this->_request->getParams ();
      unset ($params ['module'], $params ['controller'], $params ['action']);
      $this->params = $params;      
      $this->model = new Model_DbTable_StammdatenData ();     
    }        
        
    /**
     * Formular befüllen, Logo holen
     *     
     */
    public function indexAction ()
    {            
      $data = $this->model->get_address ();
      $this->view->data = $data;                        
      if (!empty ($data ['media']))              
        $this->view->logo = '/uploads/'.$data ['media'];                               
    }
    
    /**
     * Stammdaten speichern
     *  
     */
    public function editAction ()
    {         
      $this->model->update_address ($this->params);
      $this->model->history ();
      $this->_redirect ('/stammdaten/index/index');
    }        
    
  }

?>
