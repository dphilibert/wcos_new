<?php

/**
 * Formular um neue Anbieter anzulegen
 *  
 */
class Form_ProviderEdit extends Zend_Form
{
  var $action = '/admin/Anbieter/edit';
  var $name = 'provider_form_edit';
  var $method = 'POST';
  
  var $decorators = array (
    array ('HtmlTag', array ('tag' => 'div', 'style' => 'margin:5px 0px 5px 0px;')),
    array ('Label', array ('tag' => 'span', 'style' => 'float:left;margin-right:20px;width:100px;'))  
  );
  
  public function init ()
  {
    $this->setAction ($this->action)->setName ($this->name)->setMethod ($this->method);
    
    //Metadaten
    $id = new Zend_Form_Element_Text ('anbieterID');
    $id->setLabel ('Kundennummer:*')->setAttrib ('class', 'inputText')->setRequired (true)->addDecorators ($this->decorators);
    
    $name = new Zend_Form_Element_Text ('firmenname');
    $name->setLabel ('Firmenname:*')->setAttrib ('class', 'inputText')->setRequired (true)->addDecorators ($this->decorators);
    
    $config = new Zend_Config (require APPLICATION_PATH . '/configs/systems.php');
    $config = $config->toArray ();
    $systems = new Zend_Form_Element_Multiselect ('systems');
    $systems->setLabel ('Systeme:*')->setRequired (true)->addMultiOptions ($config ['selections'])
            ->setAttrib('style', 'width:172px;height:100px;')->addDecorators ($this->decorators);
    
    $level = new Zend_Form_Element_Checkbox ('premiumLevel');
    $level->setLabel ('Premium:')->addDecorators ($this->decorators);
    
    //Stammdaten
    $street = new Zend_Form_Element_Text ('strasse');
    $street->setLabel ('Straße:*')->setAttrib ('class', 'inputText')->setRequired (true)->addDecorators ($this->decorators);
    
    $no = new Zend_Form_Element_Text ('hausnummer');
    $no->setLabel ('Hnr.:*')->setAttrib ('class', 'inputText')->setRequired (true)->addDecorators ($this->decorators);
    
    $country = new Zend_Form_Element_Text ('land');
    $country->setLabel ('Land:*')->setAttrib ('class', 'inputText')->setValue ('Deutschland')->setRequired (true)->addDecorators ($this->decorators);
    
    $zip = new Zend_Form_Element_Text ('plz');
    $zip->setLabel ('Plz.:*')->setAttrib ('class', 'inputText')->setRequired (true)->addDecorators ($this->decorators);
    
    $city = new Zend_Form_Element_Text ('ort');
    $city->setLabel ('Ort:*')->setAttrib ('class', 'inputText')->setRequired (true)->addDecorators ($this->decorators);
    
    $fon = new Zend_Form_Element_Text ('fon');
    $fon->setLabel ('Tel.:*')->setAttrib ('class', 'inputText')->setRequired (true)->addDecorators ($this->decorators);
    
    $fax = new Zend_Form_Element_Text ('fax');
    $fax->setLabel ('Fax:')->setAttrib ('class', 'inputText')->addDecorators ($this->decorators);
    
    $email = new Zend_Form_Element_Text ('email');
    $email->setLabel ('E-Mail:*')->setAttrib ('class', 'inputText')->setRequired (true)->addDecorators ($this->decorators);
    
    $www = new Zend_Form_Element_Text ('www');
    $www->setLabel ('Internet:*')->setAttrib ('class', 'inputText')->setRequired (true)->addDecorators ($this->decorators);
        
    $submit = new Zend_Form_Element_Button ('submit');
    $submit->setLabel ('Speichern')->setAttrib ('class', 'buttonSave ui-corner-all')->setAttrib ('onclick', 'submit_form ("'.$this->action.'", "'.$this->name.'", "provider_editor2");');
    
    $hidden = new Zend_Form_Element_Hidden ('id');
    
    $this->addElements (array (
     $id, $name, $systems, $level,
     $street, $no, $country, $zip, $city, $fon, $fax, $email, $www,
     $submit, $hidden
    ));
  }        
}
?>
