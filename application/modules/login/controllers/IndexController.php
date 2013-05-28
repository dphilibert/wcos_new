<?php

  /**
   * Login
   *  
   */
  class Login_IndexController extends Zend_Controller_Action
  {
    /**
     * Initialisierung - Layout abschalten fuer Login-Formular (eigenes Layout)
     *     
     */
    public function init ()
    {
      $this->_helper->_layout->disableLayout ();
    }
    
    /**
     * rendert das Login-Form
     *
     * @return void
     */
    public function indexAction ()
    {      
      if ($this->getRequest ()->getParam ('error') == 'login')
        $this->view->error = true;
                      
      $this->render ('index');
    }

    /**
     * Fuehrt die Anmeldung durch
     *  
     */
    public function goAction ()
    {
      $this->_helper->viewRenderer->setNoRender ();
      $config = Zend_Registry::get ('config');                        
      $zbvs = new SoapClient (null, array('location' => $config->soap->zbvsPath, 'uri' => $config->soap->zbvsPath));
      $model = new Model_DbTable_AnbieterData ();
      $params = $this->_request->getParams ();
      
      $login = $zbvs->login ($params ['username'], $params ['password']);                        
      if ($login ['status'] == 1)
      {
        $user = $zbvs->getUserDatenFromHash ($login ['hash']);        
        
        if (empty ($user ['firmaKundennummer']))
          $this->_helper->redirector->gotoUrl ('/login/index/index/error/login');        
        $model->provider_id = $user ['firmaKundennummer'];
        $provider = $model->getAnbieterDetails (false);   
                        
        $user ['anbieterID'] = $user ['firmaKundennummer'];
        $user ['anbieterDetails'] = $provider;           
        $user ['systems'] = $zbvs->getWcosSystems ($login ['hash']);        
        
        $admin = $zbvs->checkWcosSuperuser ($login ['hash']);
        if ($admin ['status'] == 1)
        {  
          $user ['userStatus'] = -1;          
          $systems_config = new Zend_Config (require APPLICATION_PATH . '/configs/systems.php');
          $user ['systems'] =  implode (',', array_keys ($systems_config->brands->toArray ()));          
        } 
                         
        $session = new Zend_Session_Namespace ();
        $session->userData = $user;
        $session->anbieterData = $provider;
        $session->system_id = array_shift (explode (',', $user ['systems']));
        if (empty ($session->system_id))
          $session->system_id = 1;            
                
        $model->logged_in ();
        $this->_helper->redirector->gotoUrl ('/einfuehrung/index/index');
      } else
      {
        $this->_helper->redirector->gotoUrl ('/login/index/index/error/login');
      }                          
    }
    
    /**
     * Fuehrt die Abmeldung durch
     *  
     */
    public function logoutAction ()
    {
      Zend_Session::destroy (true);
      $this->_redirect ('/login');
    }
  }

?>
