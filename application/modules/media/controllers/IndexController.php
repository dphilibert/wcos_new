<?php

  /**
   * Media
   *  
   */
  class Media_IndexController extends Zend_Controller_Action
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
     * Media-Model
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
      unset ($params ['module'], $params ['controller'], $params ['action'], $params ['MAX_FILE_SIZE']);      
      $this->params = $params;
            
      if ($action == 'new' OR $action == 'edit' OR $action == 'delete')
      {
        $this->_helper->_layout->disableLayout ();
        $this->_helper->viewRenderer->setNoRender (true);
      }  
      
      $this->session = new Zend_Session_Namespace ();    
      $this->model = new Model_DbTable_MediaData ();     
    }
    
    
    /**
     * Listenansicht mit Suche und Paging
     *    
     */
    public function indexAction ()
    {            
      if (empty ($this->params ['search_term'])) $this->params ['search_term'] = '';
      if (empty ($this->params ['page'])) $this->params ['page'] = 1;      
      
      $list = $this->model->get_media_list ($this->params ['media_type'], $this->params ['search_term']);
      if (!empty ($list))
        $this->view->data_paging = $this->model->paging ($list, $this->params ['page']);
                 
      $this->view->media_type = $this->params ['media_type'];
    }
            
    /**
     * Neues Bild/Video anlegen
     *  
     */
    public function newAction ()
    {
      $form = ($this->params ['media_type'] == 1) ? new Form_Image () : new Form_Video ();    
       $this->model->mod_media_form ($form, $this->params ['media_type']);      
      if (isset ($this->params ['beschreibung']))
      {
        if ($form->isValidPartial ($this->params))
        {          
          $this->model->add_media ($this->params);
          $this->model->history ();
          echo 'success';
        } else
        {
          $form->populate ($this->params);
          echo $form;
        }          
      } else
      {
        $form->populate ($this->params);
        echo $form;
      }  
    } 
    
    /**
     * Bild/Video bearbeiten
     *  
     */
    public function editAction ()
    {
      $form = ($this->params ['media_type'] == 1) ? new Form_Image () : new Form_Video ();  
      $this->model->mod_media_form ($form, $this->params ['media_type'], 'edit');      
      if (isset ($this->params ['beschreibung']))
      {
        if ($form->isValidPartial ($this->params))
        {          
          $this->model->update_media ($this->params);
          $this->model->history ();
          echo 'success';
        } else
        {
          $form->populate ($this->params);
          echo $form;
        }          
      } else
      {
        $form->populate ($this->model->get_media_row ($this->params ['id']));
        echo $form;
      }  
    } 
    
    /**
     * Bild/Video loeschen
     *  
     */
    public function deleteAction ()
    {      
      $this->model->delete_media ($this->params ['id']);
      $this->model->history ();
    } 
    
    /**
     * Bilder/Videos aus anderen System uebernehmen
     *  
     */
    public function copyAction ()
    {      
      $this->model->copy_media ($this->params ['from_system'], $this->params ['media_type']);
      $this->model->history ();
      $this->_redirect ('/media/index/index/media_type/'.$this->params ['media_type']);
    }        
  }

?>
