<?php

class Form_Search extends Zend_Form
{
  var $action = '/admin/index/index';
  var $name = 'search';
  var $method = 'POST';
  
  var $decorators = array (
    array ('HtmlTag', array ('tag' => 'div', 'style' => 'float:left;margin-right:20px;')),   
  );
        
  public function init ()
  {
    $this->setAction ($this->action)->setName ($this->name)->setMethod ($this->method);
    
    $search = new Zend_Form_Element_Text ('search_term');
    $search->setAttrib ('class', 'searchBox')->setAttrib ('placeholder', 'Suche Name/ID')->addDecorators ($this->decorators)->removeDecorator ('Label');
                    
    $this->addElements (array (
       $search       
    ));
  }        
  
}

?>
