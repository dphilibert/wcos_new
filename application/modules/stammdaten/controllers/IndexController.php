<?php

  /**
   * Stammdaten
   *  
   */
  class Stammdaten_IndexController extends Zend_Controller_Action
  {   
    var $params;    
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
      
      //todo: alte variante, muss angepasst werden
      $logo_data = $media_model->get_media_row (5, $data ['mediaID']);      
      if (!empty ($logo_data))
      {
        $file = pathinfo ($logo_data ['media']);
        $this->view->logo = '/uploads/'.$logo_data ['id'].'.'.$file ['extension'];
      }                         
    }
    
    /**
     * Stammdaten speichern
     *  
     */
    public function editAction ()
    {     
      $this->model->update_address ($this->params);
      $this->_redirect ('/stammdaten/index/index');
    }        

    
  }

?>
