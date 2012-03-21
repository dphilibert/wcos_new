<?php

  /**
   * IndexController des Modules Login
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Login_IndexController extends Zend_Controller_Action
  {
    /**
     * disabled das Layout
     *
     * @return void
     */
    public function init ()
    {
      $this->_helper->_layout->disableLayout ();
    }


    // die Funktion getForm () liefert nur das Form fuer die Hidden-Fields username und Passwort
    // diese wurden als Hidden-Fields eingebunden, da das scrumble-Javascript sonst den Usernamen
    // sichtbar gescrumbled haette und das sieht fuer den User nicht gut aus. Deswegen username und
    // Passwort nicht als Form-Felder.
    /**
     * generiert das Login-Form
     *
     * @return Zend_Form
     */
    public function getForm ()
    {
      $loginForm = new Zend_Form;
      $loginForm->setAction ('/login/process/go');
      $loginForm->setMethod ('POST');
      $loginForm->setAttrib ('id', 'login');
      $loginForm->addElement ('submit', 'login', array('label' => 'Login', 'onClick' => 'scrambleLoginData();'));
      $loginForm->addElement ('hidden', 'username');
      $loginForm->addElement ('hidden', 'password');
      return $loginForm;
    }

    /**
     * rendert das Login-Form und gibt ggf. das Change-Passwort-Fenster aus
     *
     * @return void
     */
    public function indexAction ()
    {
      $loginForm = $this->getForm ();
      $this->view->loginForm = $loginForm;
      if ($this->getRequest ()->getParam ('error') == 'login')
      {
        $this->render ('index');
        $this->render ('loginfailure');
      }
      if ($this->getRequest ()->getParam ('error') == 'changepassword')
      {
        $this->render ('index');
        $this->render ('changepassword');
      }
      if ($this->getRequest ()->getParam ('error') == 'cpwdialog')
      {
        $response ['content'] = $this->view->render ('index/cpwdialog.phtml');
        ////logDebug (print_r ($response, true), "render");
        $this->_helper->json->sendJson ($response);
      }
    }

    /**
     * Error Tracker
     *
     * @return void
     *
     */
    public function errorAction ()
    {
      //logDebug ('loginError', 'login::ErrorAction');
    }

    /**
     * Action zum ändern des Passwortes
     *
     * @return void
     */
    public function changepasswordAction ()
    {
      $sessionNamespace = new Zend_Session_Namespace ();
      $newPassword = $this->getRequest ()->getParam ('newpw');
// TODO wie können anbieterID und Hash aus der session kommen wenn der User noch NIE eingeloggt war?
      $anbieterID = $sessionNamespace->anbieterData ['anbieterID'];
      $userHash = $sessionNamespace->userData ['hash'];
      //logDebug (print_r ($sessionNamespace->userData, true), "1");
      ////logDebug ('changing password if AnbieterID '.$anbieterID.' to '.$newPassword, 'login::changepasswordAction');
      $config = Zend_Registry::get ('config');
      $location_soap_zbvs = $config->soap->zbvsPath;
      $soap_client = new SoapClient(null, array('location' => $location_soap_zbvs,
        'uri' => $location_soap_zbvs));
      $responseCode = $soap_client->change_password ($userHash, $newPassword);
      if ($responseCode > 0)
      {
        Model_DbTable_AnbieterData::saveAnbieter ('last_login', date ('d.m.Y H:i:s'), $anbieterID);
      }
      else
      {
        logError ("ReponseCode: $responseCode", "login::changepasswordAction");
      }
      $response ['error'] = $responseCode;
      $this->_helper->json->sendJson ($response);
    }

    public function logoutAction ()
    {
      Zend_Session::destroy (true);
      $this->_redirect ('/login');
    }
  }

?>
