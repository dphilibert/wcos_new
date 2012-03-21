<?php

  /**
   * Bootstraper
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  require_once ('../library/logger.inc.php');
  require_once ('../library/general.inc.php');
  require_once ('../library/SimpleImage.php');
  require_once ('../library/qqFileUploader.inc.php');
  require_once ('Zend/Loader/Autoloader.php');
  $autoloader = Zend_Loader_Autoloader::getInstance ();
  $autoloader->isFallbackAutoloader (true);
  /*
    $applicationEnv = 'development';
    if (array_key_exists ('APPLICATION_ENV', $_ENV)) {
      $applicationEnv = $_ENV ['APPLICATION_ENV'];
    }
  */
  $applicationEnv = APPLICATION_ENV;
  $config = new Zend_Config_Ini ('../application/configs/application.ini', $applicationEnv);
  $db = Zend_Db::factory ($config->database);
  Zend_Db_Table_Abstract::setDefaultAdapter ($db);
  Zend_Registry::set ('db', $db);
  Zend_Registry::set ('config', $config);
  class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
  {

    /**
     * @var object Layout
     */
    var $layout = NULL;


    /**
     * initialisiert das Layout
     *
     * @return void
     */
    protected function _initLayout ()
    {
      //    Zend_Layout::startMvc(APPLICATION_PATH . '/../application/layouts/scripts');
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
      $autoloader = new Zend_Application_Module_Autoloader (
        array('namespace' => '',
          'basePath' => dirname (__FILE__)));
      return $autoloader;
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
     * startet die Applikation
     *
     * @return void
     */
    public function run ()
    {
      $request = new Zend_Controller_Request_Http();
      $router = new Zend_Controller_Router_Rewrite();
      $router->route ($request);
      $_module = $request->getModuleName ();
      $_controller = $request->getControllerName ();
      $this->layout->_module = $_module;
      $this->layout->_controller = $request->getControllerName ();
      $this->layout->_action = $request->getActionName ();
      ////logDebug ($_module."/".$request->getControllerName ()."/".$request->getActionName ());
      $this->bootstrap ('frontController');
      $frontController = $this->getResource ('frontController');
      $frontController->setParam ('useDefaultControllerAlways', false);
      $frontController->throwExceptions (true);
      $frontController->registerPlugin (new Zend_Controller_Plugin_ErrorHandler(
        array('module' => 'default',
          'controller' => 'error',
          'action' => 'index')));
      $pluginHashControl = new Plugin_HashControl ();
      $frontController->registerPlugin ($pluginHashControl);
      $pluginGlobalInfo = new Plugin_GlobalInfo ();
      $frontController->registerPlugin ($pluginGlobalInfo);
      $pluginGlobal = new Plugin_Global ();
      $frontController->registerPlugin ($pluginGlobal);
      // bei soap-Anfragen und Cron-Jobs erstmal keine zvbs-Auth durchfuehren TODO ggf. noch einbauen
      if ($_module != 'soap' && $_controller != 'cron')
      {
        $pluginZBVS = new Plugin_Zbvs ();
        $frontController->registerPlugin ($pluginZBVS);
        $frontController->throwExceptions (true);
      }
      Zend_Registry::set ('config', new Zend_Config_Ini ('../application/configs/application.ini', APPLICATION_ENV));
      Zend_Registry::set ('request', $request);
      $this->frontController->dispatch ();
    }
  }


