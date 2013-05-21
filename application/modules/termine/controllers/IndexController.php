<?php

/**
 * Termine
 * 
*/
class Termine_IndexController extends Zend_Controller_Action
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
    $this->model = new Model_DbTable_TermineData ();
  }        
  
 /**
  * Termine-Listenansicht mit Paging und Suche
  *  
  */
  public function indexAction ()
  {        
    if (empty ($this->params ['search_term'])) $this->params ['search_term'] = '';
    if (empty ($this->params ['page'])) $this->params ['page'] = 1;
    $list = $this->model->get_dates_list ($this->params ['search_term']);   
    if (!empty ($list))
      $this->view->data_paging = $this->model->paging ($list, $this->params ['page']);
               
    $config = new Zend_Config (require APPLICATION_PATH . '/configs/module.php');
    $this->view->date_types = $config->dates->toArray ();
  }

  /**
   * neuer Termin
   *  
   */
  public function newAction ()
  {
    $form = new Form_Dates ();
    
    if (isset($this->params ['title']))
    {
      if ($form->isValidPartial ($this->params))
      {        
        $this->model->add_date ($this->params);        
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
   * Termin bearbeiten
   *  
   */
  public function editAction ()
  {
    $form = new Form_Dates ();
    $this->model->mod_form_edit ($form, 'termine');
    
    if (isset($this->params ['title']))
    {
      if ($form->isValidPartial ($this->params))
      {        
        $this->model->edit_date ($this->params);
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
      $form->populate ($this->model->get_date ($this->params ['id']));
      echo $form;
    }  
  }        

  /**
   * Termin loeschen
   *  
   */
  public function deleteAction ()
  {    
    $this->model->delete_date ($this->params ['id']);
    $this->model->history ();
  }        
  
  /**
   * Termine aus anderen System uebernehmen
   *  
   */
  public function copyAction ()
  {    
    $this->model->copy_dates ($this->params ['from_system']);
    $this->model->history ();
    $this->_redirect ('/termine/index/index/');
  }        
  
}

?>
