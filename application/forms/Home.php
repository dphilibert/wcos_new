<?php

class Form_Home extends Zend_Form
{
  var $action = '/einfuehrung/index/premium';
  var $name = 'premium_switch';
  var $method = 'POST';
  var $decorators = array ('ViewHelper', 'Errors',
      array ('HtmlTag', array ('class' => 'controls', 'style' => 'margin: 5px 0px 5px 100px;')),
      array ('Label', array ('class' => 'control-label', 'style' => 'width:auto;'))
      );
      
  public function init ()
  {
    $this->setAction ($this->action)->setName ($this->name)->setMethod ($this->method)->setAttribs (array ('class' => 'form-horizontal', 'style' => 'margin-bottom:0px;'));
    
    $begin = new Zend_Form_Element_Text ('start');
    $begin->setLabel ('Beginn')->setDecorators ($this->decorators)->setRequired (true);
    
    $end = new Zend_Form_Element_Text ('end');
    $end->setLabel ('Ende')->setDecorators ($this->decorators)->setRequired (true);
    
    $button = new Zend_Form_Element_Button ('submit');
    $button->setLabel ('speichern')->setDecorators ($this->decorators)
            ->setAttrib ('onclick', 'submit_form ("'.$this->action.'");')->removeDecorator ('Label');
    
    $hidden = new Zend_Form_Element_Hidden ('system_id');
        
    $this->setDecorators (array ('FormElements', 'Form'));
    $this->addElements (array ($begin, $end, $button, $hidden));        
  }        
   
}
?>
