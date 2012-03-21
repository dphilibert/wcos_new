<?php

  /**
   * Prüfung der Zugangsberechtigung des einzelnen Users fuer den Controller bzw. den action-Aufruf
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Plugin_Zbvs extends Zend_Controller_Plugin_Abstract
  {


    /**
     * Prüfung via soap-Aufgruf gegen das ZBVS vor dem Dispatch
     *
     * @param Zend_Controller_Request_Abstract $request Request
     */
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
      $config = Zend_Registry::get ('config');
      $sessionNamespace = new Zend_Session_Namespace ();
      $userData = $sessionNamespace->userData;
      if (is_array ($userData) && array_key_exists ('hash', $userData))
      {
        $sessionUserHash = $userData ['hash'];
        $location_soap_zbvs = $config->soap->zbvsPath;
        $this->_module = $request->module;
        $this->_controller = $request->controller;
        $this->_action = $request->action;
        $this->_application = $config->appName;
        $soap_client = new SoapClient(null, array('location' => $location_soap_zbvs,
          'uri' => $location_soap_zbvs));
        $status = -99; // standardmaessig wird erstmal kein access gegeben
        // check nur aufrufen wenn es nicht der Login-Vorgang ist und kein soap-Aufruf
        if ($this->_module != 'login' && $this->_module != 'soap' && $this->_controller != 'verlagsmanager.ajax')
        {
          $status = $soap_client->check ($sessionUserHash, $this->_application, $this->_module, $this->_controller, $this->_action);
          //logDebug ("Status: $status / $sessionUserHash, $this->_application, $this->_module, $this->_controller, $this->_action", "Plugin_Zbvs::preDispatch");
        }
        if (!$status > 0) // Falls Aktion nicht erlaubtr -> Umleitung auf den Login-Controller (Index-Action)
        {
          logError ("Fehler beim Login", "Plugin_Zbvs::preDispatch");
          if ($this->_module != 'login')
          {
            $request->setModuleName ('login')->setControllerName ('index')->setActionName ('index');
          }
        }
      }
    }
  }

?>
