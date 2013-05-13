<?php

class Form_Dates extends Zend_Form
{
  var $action = '/termine/index/new';
  var $name = 'dates_form';
  var $method = 'POST';
  var $decorators = array ('ViewHelper', 'Errors',
      array ('HtmlTag', array ('class' => 'controls', 'style' => 'margin: 5px 0px 5px 100px;')),
      array ('Label', array ('class' => 'control-label', 'style' => 'width:auto;'))
      );
      
  public function init ()
  {
    $session = new Zend_Session_Namespace ();           
    $conf = new Zend_Config (require APPLICATION_PATH.'/configs/module.php');    
    
    $this->setAction ($this->action)->setName ($this->name)->setMethod ($this->method)->setAttribs (array ('class' => 'form-horizontal', 'style' => 'margin-bottom:0px;'));
            
    $abc = new Zend_Form_Element_Text ('title');
    $abc->setLabel ('Name*')->setDecorators ($this->decorators)->setRequired (true);
        
    $teaser = new Zend_Form_Element_Text ('teaser');
    $teaser->setLabel ('Teaser')->setDecorators ($this->decorators);
    
    $beschreibung = new Zend_Form_Element_Textarea ('beschreibung');
    $beschreibung->setLabel ('Beschreibung*')->setDecorators ($this->decorators)->setRequired (true)->setAttrib ('rows', 10);
    
    $typ = new Zend_Form_Element_Select ('typID');
    $typ->setLabel ('Typ*')->setDecorators ($this->decorators)->addMultioptions ($conf->dates->toArray ())->setRequired (true);
    
    $start = new Zend_Form_Element_Text ('beginn');
    $start->setLabel ('Beginn*')->setDecorators ($this->decorators)->setRequired (true);
    
    $ende = new Zend_Form_Element_Text ('ende');
    $ende->setLabel ('Ende*')->setDecorators ($this->decorators)->setRequired (true);
    
    $ort = new Zend_Form_Element_Text ('Ort');
    $ort->setLabel ('Ort*')->setDecorators ($this->decorators)->setRequired (true);
    
    $file = new Zend_Form_Element_File ('date_logo');
    $file->setLabel ('Bild')->removeDecorator ('DdDtWrapper')->addDecorators ($this->decorators)->removeDecorator ('ViewHelper');
    
    $button = new Zend_Form_Element_Button ('submit');
    $button->setLabel ('speichern')->setDecorators ($this->decorators)
            ->setAttrib ('onclick', 'submit_form ("'.$this->action.'");')->removeDecorator ('Label');
    
    $hidden = new Zend_Form_Element_Hidden ('anbieterID');
    $hidden->setValue ($session->anbieterData ['anbieterID'])->setDecorators ($this->decorators)->removeDecorator ('Label');
    
    $hidden2 = new Zend_Form_Element_Hidden ('system_id');
    $hidden2->setDecorators ($this->decorators)->removeDecorator ('Label')->setValue ($session->system_id);
    
    $this->setDecorators (array ('FormElements', 'Form'));
    $this->addElements (array ($abc, $teaser, $beschreibung, $typ, $start, $ende, $ort, $file, $button, $hidden, $hidden2));        
  }        
    
}
?>
