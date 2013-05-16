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
      $media_model = new Model_DbTable_MediaData ();      
      $data = $this->model->get_address ();
      $this->view->data = $data;            
      $logo_data = $media_model->get_media (5, $data ['stammdatenID']);      
      if (!empty ($logo_data))              
        $this->view->logo = '/uploads/'.$logo_data ['media'];                               
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
