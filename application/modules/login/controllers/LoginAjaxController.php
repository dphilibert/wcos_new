<?php
  /**
   * Ajax-Klasse für das Modul Login
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   *
   */
  class Login_LoginAjaxController extends Zend_Controller_Action
  {

    /**
     * schaltet vor dem Dispatching das Layout und das View-Rendering ab
     *
     * @return void
     */
    public function preDispatch ()
    {
      // fuer AJAX Layout und View render abschalten
      $this->_helper->_layout->disableLayout ();
      $this->_helper->viewRenderer->setNoRender (true);
    }

    /**
     *  Bentzernamen auf Existenz prüfen und Mail an technik dass der Benutzer ein neues Passwort möchte
     * TODO: später mal Link an User schicken mit Funktionalität neues Passwort generieren!!!
     * falls Benutzer nicht existiert -> return -1
     * json-Rückgabe 1 = alles tutti
     * json-Rückgabe -1 = Benutzername falsch
     *
     * @return void
     */
    public function passwortvergessenAction ()
    {
      // Bentzernamen auf Existenz prüfen und Mail an technik dass der Benutzer ein neues Passwort möchte
      // TODO: später mal Link an User schicken mit Funktionalität neues Passwort generieren!!!
      // falls Benutzer nicht existiert -> return -1
      // Rückgabe 1 = alles tutti
      // Rückgabe -1 = Benutzername falsch
      $config = Zend_Registry::get ('config');
      $location_soap_zbvs = $config->soap->zbvsPath;
      $soap_client = new SoapClient(null, array('location' => $location_soap_zbvs,
        'uri' => $location_soap_zbvs));
      $username = $this->getRequest ()->getParam ('username');
      $soapArray = $soap_client->login ($username, '');
      if ($soapArray ['status'] == -1)
      {
        $ret = -1;
      }
      if ($soapArray ['status'] == -2)
      {
        $ret = 1;
      }
      $this->_helper->json->sendJson ($ret);
    }
  }

?>
