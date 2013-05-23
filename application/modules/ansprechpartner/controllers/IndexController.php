<?php

  /*
   * Ansprechpartner
   * 
   */
  class Ansprechpartner_IndexController extends Zend_Controller_Action
  {
    /**
     * Parameter
     * @var array 
     */
     var $params;
              
     /**
      * Ansprechpartner-Model
      * @var object
      */
     var $model;
    
     /**
      * Initialisierung - Ajax Context, Parameter, Session und Model
      *  
      */
     public function init ()
     {
       $params = $this->_request->getParams ();
       $action = $params ['action'];
       unset ($params ['module'], $params ['controller'], $params ['action'], $params ['MAX_FILE_SIZE']);            
       $this->params = $params;
       
       if ($action == 'new' OR $action == 'edit' OR $action == 'delete')
       {
         $this->_helper->_layout->disableLayout ();
         $this->_helper->viewRenderer->setNoRender (true);
       }  
              
       $this->model = new Model_DbTable_AnsprechpartnerData ();
     }        
    
    /**
     * Ansprechpartner Listenansicht mit Paging und Suche
     *     
     */
    public function indexAction ()
    {                                   
      if (empty ($this->params ['page'])) $this->params ['page'] = 1;      
      if (empty ($this->params ['search_term'])) $this->params ['search_term'] = '';
      
      $list = $this->model->get_contacts_list ($this->params ['search_term']);                     
      if (!empty ($list))
        $this->view->data_paging = $this->model->paging ($list, $this->params ['page']);               
    }
    
    /**
     * Neuer Ansprechpartner
     *  
     */
    public function newAction ()
    {
      $form = new Form_Contacts ();      
                           
      if (isset ($this->params ['anbieterID']))
      {
        if ($form->isValidPartial ($this->params))
        {                  
          $this->model->add_contact ($this->params);
          $this->model->history ();
          echo 'success';
        } else
        {          
          $form->populate ($this->params);
          if (!empty ($this->params ['file_name']))
            $form = $this->model->add_file_info ($form->__toString (), $this->params ['file_name_orig'], $this->params ['file_name']);
          echo $form;
        }          
      } else
      {
        $form->populate ($this->params);                
        echo $form;       
      }        
    }        
    
    /**
     * Ansprechpartner bearbeiten
     *  
     */
    public function editAction ()
    {
      $form = new Form_Contacts ();
      $this->model->mod_form_edit ($form, 'ansprechpartner');
      
      if (isset ($this->params ['vorname']))
      {
        if ($form->isValidPartial ($this->params))
        {
          $this->model->update_contact ($this->params);
          $this->model->history ();
          echo 'success';
        } else
        {                                  
          $form->populate ($this->params);
          if (!empty ($this->params ['file_name']))
            $form = $this->model->add_file_info ($form->__toString (), $this->params ['file_name_orig'], $this->params ['file_name']);
          echo $form;
        }          
      } else
      {
        $form->populate ($this->model->get_contact ($this->params ['id']));        
        echo $form;
      }        
    }        
    
    /**
     * Ansprechpartner Loeschen
     *  
     */
    public function deleteAction ()
    {      
      $this->model->delete_contact ($this->params ['id']);
      $this->model->history ();
    }        
    
    /**
     * Uebernimmt die Ansprechpartner aus einem anderen System
     *  
     */
    public function copyAction ()
    {      
      $this->model->copy_contacts ($this->params ['from_system']);
      $this->model->history ();
      $this->_redirect ('/ansprechpartner/index/index');
    }        
             
  }

?>
