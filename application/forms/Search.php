<?php

class Form_Search extends Zend_Form
{
  var $action = '/admin/index/index';
  var $name = 'provider_search';
  var $method = 'POST';
  
  var $decorators = array (
    array ('HtmlTag', array ('tag' => 'div', 'style' => 'float:left;margin-right:20px;')),
    array ('Label', array ('tag' => 'span', 'style' => 'float:left;margin-right:20px;line-height:200%;margin-bottom:20px;'))  
  );
  var $label_decorators = array (
    array ('HtmlTag', array ('tag' => 'div', 'style' => 'float:left;'))  
  );
      
  public function init ()
  {
    $this->setAction ($this->action)->setName ($this->name)->setMethod ($this->method);
    
    $search = new Zend_Form_Element_Text ('search_term');
    $search->setLabel ('Suche:')->addDecorators ($this->decorators);
    
    $button = new Zend_Form_Element_Submit ('submit');
    $button->setLabel ('Start')->addDecorators ($this->label_decorators)->removeDecorator ('DtDdWrapper');
            
    $this->addElements (array (
       $search,
       $button
    ));
  }        
  
}

?>
