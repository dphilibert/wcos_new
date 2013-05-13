<?php

  /**
   * Whitepaper
   *  
   */
  class Whitepaper_IndexController extends Zend_Controller_Action
  {   
    /**
    * Parameter
    * @var array 
    */
    var $params;
    
    /**
    * Termine-Model
    * @var object
    */
    var $model;

    /**
    * Initialisierung - Ajax Context, Parameter und Model
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
     
      $this->model = new Model_DbTable_WhitepaperData ();
    }        
    
    /**
     * Whitepaper-Listenansicht mit Paging und Suche
     *    
     */
    public function indexAction ()
    {                  
      if (empty ($this->params ['search_term'])) $this->params ['search_term'] = '';
      if (empty ($this->params ['page'])) $this->params ['page'] = 1;
      $list = $this->model->get_whitepaper_list ($this->params ['search_term']);      
      if (!empty ($list))
        $this->view->data_paging = $this->model->paging ($list, $this->params ['page']);               
    }

    /**
     * neues Whitepaper
     *  
     */
    public function newAction ()
    {
      $form = new Form_Whitepaper ();
      
      if (isset ($this->params ['title']))
      {
        if ($form->isValid ($this->params))
        {
          $this->model->add_whitepaper ($this->params);
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
     * Whitepaper bearbeiten
     *  
     */
    public function editAction ()
    {
      $form = new Form_Whitepaper ();
      $button = $form->getElement ('submit');
      $button->setAttrib ('onclick', 'submit_form ("/whitepaper/index/edit")');
      $id = new Zend_Form_Element_Hidden ('id');
      $form->addElement ($id);
      
      if (isset ($this->params ['title']))
      {
        if ($form->isValid ($this->params))
        {
          $this->model->edit_whitepaper ($this->params);
          echo 'success';
        } else
        {
          $form->populate ($this->params);
          echo $form;
        }  
      } else
      {
        $form->populate ($this->model->get_whitepaper ($this->params ['id']));
        echo $form;
      }  
    }
    
    /**
     * Whitepaper loeschen
     *  
     */
    public function deleteAction ()
    {
      $this->model->delete_whitepaper ($this->params ['id']);
    }
    
    /**
     * Whitepaper aus anderen System uebernehmen
     *  
     */
    public function copyAction ()
    {
      $this->model->copy_whitepapers ($this->params ['from_system']);
      $this->_redirect ('/whitepaper/index/index');
    }        
  }

?>
