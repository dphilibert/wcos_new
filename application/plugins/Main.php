<?php
  /**
   * PlugIn fuer Zugangskontrolle und den Anbieter-Switch
   *  
   */
  class Plugin_Main extends Zend_Controller_Plugin_Abstract
  {
    /**    
     * PreDispatcher - 
     * - wenn Session abgelaufen Umleitung zum Login, 
     * - wenn nicht Admin-Benutzer auf dieses Modul zugreifen wollen - Umleitung auf "Home"
     * - neuen Anbieter in der Session setzen wenn Admin wechselt     
     * - die aktuell ausgewaehlte Medienmarkte in der Session setzen     
     * 
     */
    public function preDispatch ()
    {
      $helper = new Zend_Controller_Action_Helper_Redirector ();
      $session = new Zend_Session_Namespace ();
      $params = $this->_request->getParams ();
      $module = $params ['module'];
     
      if ($module != 'soap' AND $module != 'cron')
      {                
        if (empty ($session->userData) AND $module != 'login')
          $helper->gotoUrl ('/login/index/index');        

        if ($module == "admin")
        {
          $session = new Zend_Session_Namespace ();
          if ($session->userData ['userStatus'] != -1)                   
            $helper->gotoUrl ('/einfuehrung/index/index');        
        }

        if (!empty ($params ['sato'])) 
        {        
        $model = new Model_DbTable_AnbieterData ();
        $session->anbieterData = $model->getAnbieterByHash ($params ['sato']);                 
        }

        if (!empty ($params ['system_id']))
          $session->system_id = $params ['system_id'];            
      }
    }
  }

?>
