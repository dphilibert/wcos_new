<?php

/**
 * Suchformular
 *  
 */
class Form_Search extends Zend_Form
{
  var $action = '/admin/index/index';
  var $name = 'search';
  var $method = 'POST';            
  var $decorators = array ('ViewHelper', array ('HtmlTag', array ('tag' => 'span')));
  
  public function init ()
  {
    $session = new Zend_Session_Namespace ();
    
    $this->setAction ($this->action)->setName ($this->name)->setMethod ($this->method)->setAttrib ('style', 'margin-bottom:0px;');        
    $search = new Zend_Form_Element_Text ('search_term');
    $search->setAttribs (array ('class' => 'input-medium search-query', 'placeholder' => 'Suche', 'autofocus' => 'true'))
            ->setDecorators ($this->decorators);        
    
    $hidden = new Zend_Form_Element_Hidden ('system_id');
    $hidden->setDecorators ($this->decorators)->setValue ($session->system_id);
    
    $this->setDecorators (array ('FormElements', 'Form'));
    $this->addElements (array ($search, $hidden));
  }        
  
}

?>
