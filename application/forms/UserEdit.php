<?php

/**
 * Formular um einen neuen Benutzer anzulegen
 *  
 * 
 */
class Form_UserEdit extends Zend_Form
{
  
  var $action = '/admin/accounts/edit';
  var $name = 'user_form_edit';
  var $method = 'POST';
  
  var $decorators = array (
    array ('HtmlTag', array ('tag' => 'div', 'style' => 'margin:5px 0px 5px 0px;')),
    array ('Label', array ('tag' => 'span', 'style' => 'float:left;margin-right:20px;width:100px;'))  
  );
  
  public function init ()
  {
    $model = new Model_DbTable_Admin ();    
    $this->setAction ($this->action)->setName ($this->name)->setMethod ($this->method);
    
    $name = new Zend_Form_Element_Text ('username');
    $name->setLabel ('Benutzername:*')->setAttrib ('class', 'inputText')->setRequired (true)->addDecorators ($this->decorators);
    
    $password = new Zend_Form_Element_Text ('password');
    $password->setLabel ('Passwort:')->setAttrib ('class', 'inputText')->addDecorators ($this->decorators);
    
    $status = new Zend_Form_Element_Select ('userStatus');
    $status->setLabel ('Status:')->addMultiOptions (array (
      -1 =>  'Admin',
      0 => 'Provider' 
    ))->addDecorators ($this->decorators);
    
    $provider = new Zend_Form_Element_Select ('primaryAnbieterID');
    $provider->setLabel ('Anbieter:')->addMultiOptions ($model->provider_selections ('anbieterID'))
            ->addDecorators ($this->decorators)->setAttrib ('style', 'width:200px');
    
    $hidden = new Zend_Form_Element_Hidden ('userID');
        
    $submit = new Zend_Form_Element_Button ('submit');
    $submit->setLabel ('Speichern')->setAttrib ('class', 'buttonSave ui-corner-all')->setAttrib ('onclick', 'submit_form ("'.$this->action.'", "'.$this->name.'", "editor2");');
        
    $this->addElements (array (
      $name, $password, $status, $provider, $submit, $hidden  
    ));
  }        
}
?>
