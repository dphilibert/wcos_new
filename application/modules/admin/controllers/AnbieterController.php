<?php

/**
 *
 * Anbieter Auflistung und Bearbeitung
 *
 * @author Thomas Grahammer
 * @version $id$
 *
 **/

class Admin_AnbieterController extends Zend_Controller_Action
{
  var $params = array ();
      
  /**
   * Init
   *  
   */
  public function init ()
  {
    $params =  $this->_request->getParams ();
    unset ($params ['module'], $params ['controller'], $params ['action']);
    $this->params = $params;
  }

  /**
   * Listenansicht
   *  
   */
  public function indexAction ()
  {      
    $model = new Model_DbTable_Admin ();    
      
    $form = new Form_Search ();
    $form->setAction ('/admin/Anbieter/index');
    $form->populate ($this->params);
    $this->view->search_form = $form;
    
    $list_elements = (empty ($this->params ['search_term'])) ? $model->provider_list () : $model->provider_search ($this->params ['search_term']);
    if (!empty ($list_elements))
      $this->view->provider_paging = $model->paging ($list_elements, (!empty ($this->params ['page'])) ? $this->params ['page'] : 1, 35);
  }

  /**
   * neuer Anbieter
   *  
   */
  public function newAction ()
  {
    $this->_helper->_layout->disableLayout ();
    $this->_helper->viewRenderer->setNoRender (true);
    
    $model = new Model_DbTable_Admin ();
    $form = new Form_Provider ();
    
    if (!empty ($this->params ['land']))
      {
        if ($form->isValid ($this->params))
        {                    
          $model->provider_new ($this->params);  
          echo 'success';
        } else
        {
          $form->populate ($this->params);
          echo '<div id="provider_editor">'.$form.'</div>';
        }                  
      } else
      {
        echo '<div id="provider_editor">'.$form.'</div>';
      }      
  }

  /**
   * Anbieter bearbeiten
   *  
   */
  public function editAction ()
  {
    $this->_helper->_layout->disableLayout ();
    $this->_helper->viewRenderer->setNoRender (true);
    
    $model = new Model_DbTable_Admin ();                    
    $form = new Form_ProviderEdit ();     
                              
    if (!empty ($this->params ['anbieterID']) AND $form->isValid ($this->params))
    {                
      $model->provider_update ($this->params);      
      echo 'success';
    } else if (!empty ($this->params ['anbieterID']) AND !$form->isValid ($this->params))
    {                
      $form->populate ($this->params);
      echo '<div id="provider_editor2">'.$form.'</div>';
    } else
    {                   
      $form->populate ($model->provider_info ($this->params ['provider_id']));
      echo '<div id="provider_editor2">'.$form.'</div>';
    }      
  }
  
  /**
   * Anbieter loeschen
   *  
   */
  public function deleteAction ()
  {
    $this->_helper->_layout->disableLayout ();
    $this->_helper->viewRenderer->setNoRender (true);
    
    $model = new Model_DbTable_Admin ();
    $model->provider_delete ($this->_request->getParam ('provider_id'));      
  }
}

?>
