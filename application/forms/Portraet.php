<?php

class Form_Portraet extends Zend_Form
{
  var $action = '/firmenportrait/index/new';
  var $name = 'portraet_form';
  var $method = 'POST';
  var $decorators = array ('ViewHelper', 'Errors',
      array ('HtmlTag', array ('class' => 'controls', 'style' => 'margin: 5px 0px 5px 100px;')),
      array ('Label', array ('class' => 'control-label', 'style' => 'width:auto;'))
      );
      
  public function init ()
  {
    $session = new Zend_Session_Namespace ();      
    $mod_conf = new Zend_Config (require APPLICATION_PATH.'/configs/module.php');    
    $profile_validator = new Profile_Validator ();
    
    $this->setAction ($this->action)->setName ($this->name)->setMethod ($this->method)->setAttribs (array ('class' => 'form-horizontal', 'style' => 'margin-bottom:0px;'));
    
    $type = new Zend_Form_Element_Select ('type');
    $type->setLabel ('Typ')->setDecorators ($this->decorators)->setRequired (true)
            ->addMultiOptions ($mod_conf->profiles->toArray ())->addValidator ($profile_validator);
           
    $portraet = new Zend_Form_Element_Textarea ('value');
    $portraet->setDecorators ($this->decorators)->setRequired (true)
            ->setAttrib ('style', 'width:580px;');    
    
    $button = new Zend_Form_Element_Button ('submit');
    $button->setLabel ('speichern')->setDecorators ($this->decorators)
            ->setAttrib ('onclick', 'submit_form ("'.$this->action.'", "value");')->removeDecorator ('Label');
    
    $hidden = new Zend_Form_Element_Hidden ('anbieterID');
    $hidden->setValue ($session->anbieterData ['anbieterID'])->setDecorators ($this->decorators)->removeDecorator ('Label');
    
    $hidden2 = new Zend_Form_Element_Hidden ('system_id');
    $hidden2->setDecorators ($this->decorators)->removeDecorator ('Label')->setValue ($session->system_id);
    
    $this->setDecorators (array ('FormElements', 'Form'));
    $this->addElements (array ($portraet, $type, $button, $hidden, $hidden2));        
  }        
    
}
?>
