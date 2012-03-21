<?php

class Zend_View_Helper_Menu
{
  public $view;

  public function setView ()
  {
    $this->view = $view;
  }

  public function Menu ($parentID)
  {
    if ($parentID == null) $parentID = 0;
    $menuModel = new Model_DbTable_Menu ();
    $menu = $menuModel->getMenu ($parentID);
    $_auth = Zend_Auth::getInstance ();
    $_acl = new Model_Acl ();
    $_userInfo = $_auth->getStorage ()->read ();
    $_module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
    $_controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
    $myMenu = NULL;
    if ($parentID == 0)
    {
      $myMenu [0] ['menuTitle'] = "Home";
      $myMenu [0] ['menuLink'] = '/';
    }
    if (count ($menu) > 0)
    {
      foreach ($menu as $menuID => $menuArray)
      {
        $action = '*';
        $linkString = $menuArray ['menuLink'];
        $foo = explode ('/', $linkString);
        $module = $foo [1];
        $controller = $foo [2];
        if ($parentID == 0) // ist Topmenu, also Controller und Action = *
        {   
          $controller = '*';
        }
        if ($_acl->isAllowed ($_userInfo->userID, $module, $controller, $action)) 
        {
          $myMenu [$menuID] = $menuArray;
        }
      }
    }
    return $myMenu;
  }
}

?>

