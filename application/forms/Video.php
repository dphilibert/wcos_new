<?php
class Form_Video extends Zend_Form
{
  var $action = '/media/index/new/media_type/1';
  var $name = 'media_form';
  var $method = 'POST';
  var $decorators = array ('ViewHelper', 'Errors',
      array ('HtmlTag', array ('class' => 'controls', 'style' => 'margin: 5px 0px 5px 100px;')),
      array ('Label', array ('class' => 'control-label', 'style' => 'width:auto;'))
      );
      
  public function init ()
  {
    $session = new Zend_Session_Namespace ();
           
    $this->setAction ($this->action)->setName ($this->name)->setMethod ($this->method)->setAttribs (array ('class' => 'form-horizontal', 'style' => 'margin-bottom:0px;'));
            
    $beschreibung = new Zend_Form_Element_Textarea ('beschreibung');
    $beschreibung->setLabel ('Beschreibung*')->setDecorators ($this->decorators)->setRequired (true)->setAttrib ('rows', 5);
    
    $embedd_code = new Zend_Form_Element_Textarea ('media');
    $embedd_code->setLabel ('Embeddcode*')->setDecorators ($this->decorators)->setRequired (true)->setAttrib ('rows', 10);
    
    $button = new Zend_Form_Element_Button ('submit');
    $button->setLabel ('speichern')->setDecorators ($this->decorators)
            ->setAttrib ('onclick', 'submit_form ("'.$this->action.'");')->removeDecorator ('Label');
    
    $hidden = new Zend_Form_Element_Hidden ('anbieterID');
    $hidden->setValue ($session->anbieterData ['anbieterID'])->setDecorators ($this->decorators)->removeDecorator ('Label');
    
    $hidden2 = new Zend_Form_Element_Hidden ('system_id');
    $hidden2->setDecorators ($this->decorators)->removeDecorator ('Label')->setValue ($session->system_id);
    
    $hidden3 = new Zend_Form_Element_Hidden ('media_type');
    $hidden3->setDecorators ($this->decorators)->removeDecorator ('Label');
    
    $this->setDecorators (array ('FormElements', 'Form'));
    $this->addElements (array ($beschreibung, $embedd_code, $button, $hidden, $hidden2, $hidden3));        
  }        
    
}
?>
