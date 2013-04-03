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
            
      //Anbieterauswahl           
      $options = array_merge (array ('0' => '--bitte wählen--'), $model->provider_selections ());      
      $this->view->selections = $options;                 
      
      //Suchformular
      $form = new Form_Search ();
      $form->populate ($params);
      $this->view->form = $form;
      
      //Suchergebnisse
      if (!empty ($params ['search_term']))
      {
        $results = $model->provider_selection_search ($params ['search_term']);
        if (!empty ($results))
          $this->view->search_results_paging = $model->paging ($results, $params ['page']);                  
      }  
    }
  }

?>
