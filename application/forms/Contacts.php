<?php

class Form_Contacts extends Zend_Form
{
  var $action = '/ansprechpartner/index/new';
  var $name = 'contacts_form';
  var $method = 'POST';
  var $decorators = array ('ViewHelper', 'Errors',
      array ('HtmlTag', array ('class' => 'controls', 'style' => 'margin: 5px 0px 5px 100px;')),
      array ('Label', array ('class' => 'control-label', 'style' => 'width:auto;'))
      );  
  
  public function init ()
  {
    $session = new Zend_Session_Namespace ();
    $config = new Zend_Config (require APPLICATION_PATH . '/configs/systems.php');
        
    $this->setAction ($this->action)->setName ($this->name)->setMethod ($this->method)->setAttribs (array ('class' => 'form-horizontal', 'style' => 'margin-bottom:0px;'));
    
    $vorname = new Zend_Form_Element_Text ('vorname');
    $vorname->setLabel ('Vorname*')->setDecorators ($this->decorators)->setRequired (true);
    
    $nachname = new Zend_Form_Element_Text ('nachname');
    $nachname->setLabel ('Name*')->setDecorators ($this->decorators)->setRequired (true);
    
    $abteilung = new Zend_Form_Element_Text ('abteilung');
    $abteilung->setLabel ('Abteilung')->setDecorators ($this->decorators);
    
    $position = new Zend_Form_Element_Text ('position');
    $position->setLabel ('Position')->setDecorators ($this->decorators);
    
    $telefon = new Zend_Form_Element_Text ('telefon');
    $telefon->setLabel ('Telefon*')->setDecorators ($this->decorators)->setRequired (true);

    $telefax = new Zend_Form_Element_Text ('telefax');
    $telefax->setLabel ('Telefax')->setDecorators ($this->decorators);
    
    $email = new Zend_Form_Element_Text ('email');
    $email->setLabel ('eMail*')->setDecorators ($this->decorators)->setRequired (true);
    
    $system_id = new Zend_Form_Element_Select ('system_id');
    $system_id->setLabel ('Medienmarke*')->addMultiOptions ($config->brands->toArray ())->setDecorators ($this->decorators)->setRequired (true);
        
    $image = new Zend_Form_Element_File ('image');
    $image->setLabel ('Bild')->removeDecorator ('DdDtWrapper')->addDecorators ($this->decorators)
            ->removeDecorator ('ViewHelper')->setAttrib ('onchange', 'upload(this);');
    
    $button = new Zend_Form_Element_Button ('submit');
    $button->setLabel ('speichern')->setDecorators ($this->decorators)
            ->setAttrib ('onclick', 'submit_form ("'.$this->action.'");')->removeDecorator ('Label');
    
    $hidden = new Zend_Form_Element_Hidden ('anbieterID');
    $hidden->setValue ($session->anbieterData ['anbieterID'])->setDecorators ($this->decorators)->removeDecorator ('Label');
    
    $hidden2 = new Zend_Form_Element_Hidden ('system_id');
    $hidden2->setDecorators ($this->decorators)->removeDecorator ('Label')->setValue ($session->system_id);
    
    $filename = new Zend_Form_Element_Hidden ('file_name');
    $filename->setDecorators ($this->decorators)->removeDecorator ('Label');
    
    $filename_orig = new Zend_Form_Element_Hidden ('file_name_orig');
    $filename_orig->setDecorators ($this->decorators)->removeDecorator ('Label');
    
    $this->setDecorators (array ('FormElements', 'Form'));
    $this->addElements (array ($vorname, $nachname, $abteilung, $position, $telefon, $telefax, $email, $system_id, $image, $button, $hidden, $hidden2, $filename, $filename_orig));        
  }        
    
}
?>
