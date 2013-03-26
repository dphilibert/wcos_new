<?php

  /**
   * Benutzerverwaltung
   *  
   */
  class Admin_AccountsController extends Zend_Controller_Action
  {
    var $params = array ();
    
    public function init ()
    {
      $params = $this->_request->getParams ();
      unset ($params ['module'], $params ['controller'], $params ['action']);
      $this->params = $params;
    }        
    
    /**
     * Listenansicht und Formular fuer neue Benutzer
     *
     * @param void
     * @return void
     */
    public function indexAction ()
    {
      $model = new Model_DbTable_Admin ();
                  
      $form = new Form_Search ();
      $form->setAction ('/admin/accounts/index');
      $form->populate ($this->params);
      $this->view->search_form = $form;
            
      $this->view->userlist_paging = $model->paging (
              (empty ($this->params ['search_term'])) ? $model->user_list () : $model->user_search ($this->params ['search_term']),
              (!empty ($this->params ['page'])) ? $this->params ['page'] : 1, 35);       
    }
    
    /**
     * Ajax-Action fuer neue Benutzer
     * 
     * @param void
     * @return string Formular oder Erfolgsmeldung
     */
    public function newAction ()
    {
      $this->_helper->_layout->disableLayout ();
      $this->_helper->viewRenderer->setNoRender (true);
                              
      $model = new Model_DbTable_Admin ();                
      $form = new Form_User ();
                  
      if (!empty ($this->params ['username']))
      {
        if ($form->isValid ($this->params))
        {
          $model->user_new ($this->params);  
          echo 'success';
        } else
        {
          $form->populate ($this->params);
          echo '<div id="editor">'.$form.'</div>';
        }                  
      } else
      {
        echo '<div id="editor">'.$form.'</div>';
      }             
    }
    
    /**
     * Ajax-Action zum editieren der Benutzer - inkl. Formular
     * 
     * @param void
     * @return string Formular oder Erfolgsmeldung
     */
    public function editAction ()
    {
      $this->_helper->_layout->disableLayout ();
      $this->_helper->viewRenderer->setNoRender (true);
      
      $model = new Model_DbTable_Admin ();                    
      $form = new Form_UserEdit ();     
                              
      if (!empty ($this->params ['userID']) AND $form->isValid ($this->params))
      {                
        $model->user_update ($this->params);
        echo 'success';
      } else if (!empty ($this->params ['userID']) AND !$form->isValid ($this->params))
      {                
        $form->populate ($this->params);
        echo '<div id="editor2">'.$form.'</div>';
      } else
      {                
        $form->populate ($model->user_info ($this->params ['user_id']));
        echo '<div id="editor2">'.$form.'</div>';
      }              
    }        
    
    /**
     * Ajax-Action zum loeschen von Benutzern
     * 
     * @param void
     * @return void 
     */
    public function deleteAction ()
    {
      $this->_helper->_layout->disableLayout ();
      $this->_helper->viewRenderer->setNoRender (true);
      
      $model = new Model_DbTable_Admin ();
      $model->user_delete ($this->_request->getParam ('user_id'));      
    }        
        
  }

?>
