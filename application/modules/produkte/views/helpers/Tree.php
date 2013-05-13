<?php

/**
 * View-Helper für Baum-Darstellung des Produktspektrums
 *  
 */
class Produkte_View_Helper_Tree extends Zend_View_Helper_Abstract
{
  
  /**
   * Gibt den Produktbaum aus
   * 
   * @param array $data Produktbaum-Array
   * @param bool|void $toggle Flag ob der Baum aufgeklappt oder eingeklappt dargestellt werden soll
   * @param int|void $level aktuelle Ebene
   * @param string|void $ident ID zur Listenidentifizierung einklappen/ausklappen
   * 
   * @return string Produktbaum 
   */
  public function Tree ($data, $toggle = false, $level = 0, $ident = '')
  {     
    
    if (empty ($data))
      return '<div class="alert" style="text-align:center;"><b>kein Produktspektrum</b></div>';
    
    $chevron = ($toggle === false) ? 'down' : 'up';
    $display = ($toggle === false AND $level > 0) ? 'style="display:none;"' : '';        
    $action_ident = ($toggle === false) ? 'add' : 'remove';
        
    $tree = '<ul id="'.$ident.'" class="products_'.$level.'" '.$display.'>';
    foreach ($data as $key => $branch)
    {            
      $ident = sha1 ($key.$level.time ().rand ());
      $all_button = ($level > 0) ? '<button class="btn" style="padding:0px 4px 0px 4px;margin-right:5px;" onclick="code_toggle_all (\''.$ident.'\', \''.$action_ident.'\', this.children [0])"><i class="icon-ok"></i></button>' : '';
      $tree .= '<li><div class="product_level_'.$level.'">
        <div class="fl">'.$all_button.$key.'</div>
        <div class="fr"><button class="btn" style="padding:0px 4px 0px 4px;" onclick="$(\'#'.$ident.'\').slideToggle ();chevronToggle (this.children [0]);">
          <i class="icon-chevron-'.$chevron.'"></i></button>
        </div>
        <div class="clear"></div></div></li>';
                      
      if (empty ($branch [0]))
      {  
        $tree .= $this->Tree ($branch, $toggle, $level + 1, $ident);
      } else
      {
        $tree .= '<ul id="'.$ident.'" class="products_'.($level + 1).'" '.$display.'>';
        foreach ($branch as $last)
          $tree .= '<li id ="'.$action_ident.'_'.$last ['code'].'" onclick="code_toggle ('.$last ['code'].', \''.$action_ident.'\');">'.$last ['name'].'</li>';
        $tree .= '</ul>';                
      }  
      $tree .= '</li>';            
    }  
    $tree .= '</ul>';
            
    return $tree;
  }        
  
}

?>