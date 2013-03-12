<?php

class Zend_View_Helper_LoginStatus
{
  public $view = NULL;

   public function LoginStatus ()
  {
    $usedModule = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
    $usedController = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
    $usedAction = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
    $usedParameter = Zend_Controller_Front::getInstance ()->getRequest ()->getParams ();

    $loginStatus = 1;
    if ($usedModule === 'login') $loginStatus = -1;
    
    return $loginStatus;
  }
}

?>

