<?php

  /**
   * Index Controller - fuer die Admin-Anbieterauswahl
   *
   *    
   */
  class Admin_IndexController extends Zend_Controller_Action
  {

    var $params;
    
    var $model;
    
    /**
     * Ajax-Context, Params und Model
     *  
     */
    public function init ()
    {
      $this->params = $this->_request->getParams ();
      $action = $this->params ['action'];
      $this->model = new Model_DbTable_Admin;
      
      if ($action == 'upload' OR $action == 'remove')
      {
        $this->_helper->_layout->disableLayout ();
        $this->_helper->viewRenderer->setNoRender (true);       
      }        
    }        
    
    /**
     * Anbieter-Suche Kundennr./Name und Auswahl
     *   
     */
    public function indexAction ()
    {                
      if (empty ($this->params ['page'])) $this->params ['page'] = 1;
      unset ($this->params ['sato'], $this->params ['submit']);
      $this->view->url_params = $this->params;
                   
      $this->view->selections = array_merge (array ('0' => '---- Anbieter auswählen ----'), $this->model->provider_selections ());                                
      $results = $this->model->provider_selection_search (!empty ($this->params ['search_term']) ? $this->params ['search_term'] : '');
      if (!empty ($results))
        $this->view->search_results_paging = $this->model->paging ($results, $this->params ['page']);                  
        
    }
    
    /**
     * Speichert die per ajax/filereader hochgeladenen bilder ab
     *  
     */
    public function uploadAction ()
    {                              
      $img = explode (',', $this->params ['image']);
      $info = pathinfo ($this->params ['filename']);                
      $filename_new = md5 ($info ['filename'].rand ().time ());      
      file_put_contents (APPLICATION_PATH . '/../public/uploads/'.$filename_new.'.'.$info ['extension'], base64_decode ($img [1]));
      echo json_encode (array ('orig' => $info ['filename'].'.'.$info ['extension'], 'name' => $filename_new.'.'.$info ['extension']));
    }       
    
    /**
     * entfernt ein hochgeladenes Bild
     *  
     */
    public function removeAction ()
    {            
      $path = APPLICATION_PATH . '/../public/uploads/';
      if (!empty ($this->params ['file']) AND file_exists ($path.$this->params ['file']))
        unlink ($path.$this->params ['file']);      
      if (!empty ($this->params ['media_id']))
      {
        $model = new Model_DbTable_MediaData ();
        $model->delete_media ($this->params ['media_id']);
      }  
    }        
    
  }

?>
