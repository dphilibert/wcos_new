<?php

  /**
   * Klasse zum nachladen einer Seite via Ajax
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Api_PageloaderAjaxController extends Zend_Controller_Action
  {

    /**
     * setzt diverse Parameter vor dem Dispatching
     *
     * @return void
     *
     */
    public function preDispatch ()
    {
      // fuer AJAX Layout und View render abschalten
      $this->_helper->_layout->disableLayout ();
      $this->_helper->viewRenderer->setNoRender (true);
      $sessionNamespace = new Zend_Session_Namespace ();
      $sessionUserHash = $sessionNamespace->userData->userHash;
      $paramUserHash = $this->getRequest ()->getParam ('userhash');
    }


    /**
     * setzten verschiedener Kontexte für das ajax-Handling
     *
     * @return void
     */
    public function init ()
    {
      $ajaxContext = $this->_helper->getHelper ('AjaxContext');
      $ajaxContext->addActionContext ('view', 'html')
      ->addActionContext ('form', 'html')
      ->addActionContext ('test', 'xml')
      ->initContext ();
    }

    /**
     * keine Funktionialität. Wird nur gesetzt, damit er Dispatcher keine Exception wirft im Falle eines index-Dispatchings
     *
     * @return void
     */
    public function indexAction ()
    {
    }


    /**
     * lädt die angegebene Seite und liefert diese als json aus
     *
     * @return void
     */
    public function loadAction ()
    {
      $view = new Zend_View ();
      $paramPage = $this->getRequest ()->getParam ('page');
      $response = "";
      // Hier wird ein bisschen ZendFramework MVC-Technik nachgebaut, da ja schon die loadAction gerufen wird per Ajax und Sub-Aufrufe nicht moeglich sind
      $_module = $this->getRequest ()->getParam ('loadmodule');
      $_action = $this->getRequest ()->getParam ('loadaction');
      $_controller = $this->getRequest ()->getParam ('loadcontroller');
//TODO Sicherheit erhoehen durch weitere Checks da sonst XSS moeglich!
      $controllerPath_fs = APPLICATION_PATH . '/modules/' . urlencode ($_module) . '/controllers/' . urlencode ($_controller) . 'Controller.php';
      if (file_exists ($controllerPath_fs))
      {
        include ($controllerPath_fs);
        $controllerClassName = $_module . '_' . $_controller . 'Controller';
        $actionName = $_action . 'Action';
        if (class_exists ($controllerClassName))
        {
          $controllerClass = new $controllerClassName ($this->getRequest (), $this->getResponse ());
          if (method_exists ($controllerClass, $actionName)) {
            call_user_func (array(&$controllerClass, $actionName));
          }
        }
      }
      $view = $this->view; // damit sind die View-Eigenschaften aus den Sub-Aufrufen (Sub-Methoden) auch im View verfuegbar
      $viewPath_fs = APPLICATION_PATH . '/modules/' . urlencode ($_module) . '/views/scripts/' . urlencode ($_action) . '/';
      $viewFile = urlencode ($paramPage) . '.phtml';
      if (file_exists ($viewPath_fs . $viewFile))
      {
        $view->addScriptPath ($viewPath_fs);
        $response ['html'] = $view->render ($viewFile);
      }
      else
      {
        $response ['html'] = "file not found";
      }
      $this->_helper->json->sendJson ($response);
    }
  }

?>
