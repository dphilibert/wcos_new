<?php

  /**
   * Firmenportraits
   *  
   */
  class Firmenportrait_IndexController extends Zend_Controller_Action
  {

    /**
     * Parameter
     * @var array 
     */
    var $params;
    
    /**
     * Session
     * @var object 
     */
    var $session;
    
    /**
     * Firmenportraet-Model
     * @var object 
     */
    var $model;
    
    /**
     * Initialisierung - Context Ajax, Parameter, Session und Model
     *  
     */
    public function init ()
    {
      $params = $this->_request->getParams ();      
      $action = $params ['action'];
      unset ($params ['module'], $params ['controller'], $params ['action']);      
      $this->params = $params;
            
      if ($action == 'new' OR $action == 'edit' OR $action == 'delete')
      {
        $this->_helper->_layout->disableLayout ();
        $this->_helper->viewRenderer->setNoRender (true);
      }  
      
      $this->session = new Zend_Session_Namespace ();    
      $this->model = new Model_DbTable_FirmenportraitData ();     
    }        
    
    /**
     * Listenansicht
     *   
     */
    public function indexAction ()
    {                
      $this->view->data = $this->model->get_profile_list ();      
    }
    
    /**
     * neues Firmenportraet hinzufuegen
     *  
     */
    public function newAction ()
    {
      $form = new Form_Portraet ();        
      
      if (isset ($this->params ['type']))
      {
        if ($form->isValid ($this->params))
        {                   
          $this->model->add_profile ($this->params);
           $this->model->history ();
          echo 'success';
        } else
        {                    
          $form->populate ($this->params);
          echo $form;
        }          
      } else
      {
        echo $form;  
      }  
    }        
    
    /**
     * Firmenportraet bearbeiten
     *  
     */
    public function editAction ()
    {
      $form = new Form_Portraet ();      
      $this->model->mod_form_edit ($form, 'firmenportrait');
      
      if (isset ($this->params ['type']))
      {
        if ($form->isValid ($this->params))
        {                   
          $this->model->update_profile ($this->params);
           $this->model->history ();
          echo 'success';
        } else
        {
          $form->populate ($this->params);
          echo $form;
        }          
      } else
      {                
        $form->populate ($this->model->get_profile ($this->params ['id']));
        echo $form;
      }        
    }
    
    /**
     * Firmenportraet loeschen
     *  
     */
    public function deleteAction ()
    {      
      $this->model->delete_profile ($this->params ['id']);
      $this->model->history ();
    }        
     
    /**
     * Uebernimmt die Firmenportraets aus einem anderen System
     *  
     */
    public function copyAction ()
    {      
      $this->model->copy_profiles ($this->params ['from_system']);
      $this->model->history ();
      $this->_redirect ('/firmenportrait/index/index');
    }        
    
  }

?>
