<?php
     
  /**
   * Initialisierungen und Konfiguration
   *  
   */
  class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
  {

    /**
     * @var object Layout
     */
    var $layout = NULL;

    /**
     * Initialisiert das Layout
     *
     * @return void
     */
    protected function _initLayout ()
    {      
      $this->layout = new Zend_Layout ();
      $this->layout->startMvc (APPLICATION_PATH . '/../application/layouts/scripts');
    }

    /**
     * initialisiert den Autoloader
     *
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAutoload ()
    {                  
      $autoloader = new Zend_Application_Module_Autoloader (array (
          'namespace' => '',
          'basePath' => dirname (__FILE__)
          ));                  
      
      return $autoloader;
    }

    /**
     * Einbinden der Klassen im Verzeichnis "library"
     *  
     */
    protected function _initLibrary ()
    {
      //todo: das geht doch besser
      $library = APPLICATION_PATH.'/library/';      
      Zend_Loader::loadFile ('SimpleImage.php', $library);
      Zend_Loader::loadFile ('general.inc.php', $library);
      Zend_Loader::loadFile ('logger.inc.php', $library);
      Zend_Loader::loadFile ('qqFileUploader.inc.php', $library);
      Zend_Loader::loadFile ('Profile_Validator.php', $library);
    }        
    
    /**
     * Initialisiert den Datenbankadapter
     *  
     */
    protected function _initDatabase ()
    {
      $config = new Zend_Config_Ini ('../application/configs/application.ini', APPLICATION_ENV);
      $db = Zend_Db::factory ($config->database);
      Zend_Db_Table_Abstract::setDefaultAdapter ($db);
      Zend_Registry::set ('db', $db);
     
    }        
    
    /**
     * initialisiert die Helper
     *
     * @return void
     */
    protected function _initHelpers ()
    {
      $this->view = new Zend_View();
      $view = $this->view;
      $view->addHelperPath ('../application/views/helpers');
      $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
      $viewRenderer->setView ($view);
      Zend_Controller_Action_HelperBroker::addHelper ($viewRenderer);
      $ajaxContext = new Zend_Controller_Action_Helper_AjaxContext();
      Zend_Controller_Action_HelperBroker::addHelper ($ajaxContext);
    }

    /**
     * initialisiert die Navigation
     *
     * @return void
     */
    protected function _initNavigation ()
    {
      $config = NULL;
      $view = $this->view;
      $navConfig = new Zend_Config_Xml (APPLICATION_PATH . '/configs/navigation.xml', 'nav');
      $this->container = new Zend_Navigation ($navConfig);
      $this->config = $config;
      $view->navigation ($this->container);
    }

    /**
     * Initialisierung des Paginators
     *  
     */
    protected function _initPaginator ()
    {
      Zend_Paginator::setDefaultScrollingStyle ('Sliding');
      Zend_View_Helper_PaginationControl::setDefaultViewPartial (array ('paging.phtml', 'default'));
    }        

    /**
     * Initialisierung der Validatoren
     *  
     */
    protected function _initValidator ()
    {
      $config = new Zend_Config (require 'configs/form_errors-de.php');
      $translator = new Zend_Translate ('array', $config->toArray (), 'de');            
      Zend_Validate_Abstract::setDefaultTranslator ($translator);      
    }        
    
    /**
     * Initialisiert die PlugIns
     *  
     */
    protected function _initPlugIns ()
    {
      $front = Zend_Controller_Front::getInstance ();
      $front->registerPlugin (new Zend_Controller_Plugin_ErrorHandler(
              array ('module' => 'default', 'controller' => 'error', 'action' => 'index')));            
      $front->registerPlugin (new Plugin_Main ());            
    }        
             
    /**
     * startet die Applikation
     *
     * @return void
     */
    public function run ()
    {
      $request = new Zend_Controller_Request_Http();
      $router = new Zend_Controller_Router_Rewrite();
      $router->route ($request);
      
      $this->layout->_module = $request->getModuleName ();
      $this->layout->_controller = $request->getControllerName ();
      $this->layout->_action = $request->getActionName ();     
      
      $this->bootstrap ('frontController');
      
      $frontController = $this->getResource ('frontController');
      $frontController->setParam ('useDefaultControllerAlways', false);
      $frontController->throwExceptions (true);
                                      
      Zend_Registry::set ('config', new Zend_Config_Ini ('../application/configs/application.ini', APPLICATION_ENV));
      Zend_Registry::set ('request', $request);
      
      $this->frontController->dispatch ();
    }
  }


