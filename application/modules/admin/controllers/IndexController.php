<?php

  /**
   * Modul Admin - Index
   *
   * @author Thomas Grahammer
   * @version $id$
   */
  class Admin_IndexController extends Zend_Controller_Action
  {

    /**
     * setzt initial-Werte für das View
     *
     * @return void
     */
    public function init ()
    {
      $sessionNamespace = new Zend_Session_Namespace ();
      $userData = $sessionNamespace->userData;
      $anbieterID = $userData ['anbieterID'];
      $ansprechpartnerModel = new Model_DbTable_AnsprechpartnerData ();
      $this->view->anbieterID = $anbieterID;
      try
      {
        $anbieterModel = new Model_DbTable_AnbieterData ();
        $anbieterDetails = $anbieterModel->getAnbieterDetails ($anbieterID);
        $this->view->anbieterHash = $anbieterDetails ['ANBIETERHASH'];
      }
      catch (Zend_Exception $e)
      {
        $redirect = new Zend_Controller_Action_Helper_Redirector();
        $redirect->gotoUrl ('/login');
      }
    }

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
