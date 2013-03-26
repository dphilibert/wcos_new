<?php

  /**
   * Index Controller - fuer die Admin-Anbieterauswahl
   *
   *    
   */
  class Admin_IndexController extends Zend_Controller_Action
  {

    /**
     * Anbieter-Suche Kundennr./Name und Auswahl
     *
     * @param void
     * @return void
     */
    public function indexAction ()
    {
      //Model und Parameter
      $model = new Model_DbTable_Admin ();      
      $params = $this->_request->getParams ();
      if (empty ($params ['page'])) $params ['page'] = 1;
      unset ($params ['sato'], $params ['submit']);
      $this->view->url_params = $params;
            
      //Suchformular und Anbieterauswahl
      $session = new Zend_Session_Namespace ();
      $form = new Form_Search ();
      $form->populate ($params);
      $this->view->active_provider = $session->anbieterData ['anbieterhash'];                        
      $this->view->selections = $model->provider_selections ();                 
      $this->view->form = $form;
                          
      //Suchergebnisse
      if (!empty ($params ['search_term']))                                                              
        $this->view->search_results_paging = $model->paging ($model->provider_selection_search ($params ['search_term']), $params ['page']);                          
    }
  }

?>
