<?php
class Default_View_Helper_NavLink extends Zend_View_Helper_Abstract
{
  public function navLink($pageId, $label)
  {
    // Get navigation container
    $container = $this->view->navigation()->getContainer();
 
    // Get the page
    $page = $container->findOneBy('id', $pageId);
 
    // If the page is found, generate a link 
    // with the specified label text
    if (isset($page))
    {
      $html = sprintf('<a href="%s">%s</a>', $page->getHref(), $label);
    }
    // Otherwise just return the label text 
    else
    {
      $html = $label;
    }
 
    return $html;
  }
}
?>
