<?php

  /**
   * Login-Steuerung (Process) für das Login-Modul
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Login_ProcessController extends Zend_Controller_Action
  {

    /**
     * View abschalten
     *
     * @return void
     */
    public function init ()
    {
      $this->_helper->viewRenderer->setNoRender ();
    }

    /**
     * leere Funktion damit beim Aufruf von index keine Exception generiert wird
     *
     * @return void
     */
    public function indexAction ()
    {
    }

    /**
     * führt das Login durch und redirected im Erfolgsfall zur Einführung
     *
     * @return void
     *
     */
    public function goAction ()
    {
      $config = Zend_Registry::get ('config');
      $anbieterModel = new Model_DbTable_AnbieterData ();
      $location_soap_zbvs = $config->soap->zbvsPath;
      $soap_client = new SoapClient(null, array('location' => $location_soap_zbvs,
        'uri' => $location_soap_zbvs));
      $vars = $this->_request->getPost ();
      
      $vars ['username'] = base64_decode ($vars ['username']);
      $vars ['password'] = base64_decode ($vars ['password']);
      
      //Prüfen ob username in User-Tabelle (dann wäre es ein Admin-User)
      $userModel = new Model_DbTable_UserData();
      $userArray = $userModel->userExists ($vars ['username']);
      if ($userArray == false)
      {
        if ($vars ['username'] != '')
        {
          if ($vars ['password'] != '')
          {
            // Username vorhanden und Passwort vorhanden. Beides an den LP schicken
            $username = $vars ['username'];
            $password = $vars ['password'];
            $userArray = $soap_client->login ($username, $password);
            
            $userArray ['userDaten'] = $soap_client->get_userdaten ($userArray ['hash']);
            logDebug (print_r ($userArray, true), "foo");
            $userID = $userArray ['userDaten'] ['user_id'];
            
            $firmaKundennummer = $userArray ['userDaten'] ['firmaKundennummer'];
            
            if ($userID > 0)
            {
              $anbieterArray = $anbieterModel->getAnbieterByKundennummer ($firmaKundennummer);
            }
            
            $anbieterID = $anbieterArray ['anbieterID'];
            $userArray ['anbieterID'] = $anbieterID;
            if ($anbieterID > 0)
            {
              $userArray ['anbieterDetails'] = $anbieterModel->getAnbieterDetails ($anbieterID);
            }
            $sessionNamespace = new Zend_Session_Namespace ();
            if (is_array ($userArray))
            {
              $sessionNamespace->userData = $userArray;
            }
            if (is_array ($anbieterArray))
            {
              $sessionNamespace->anbieterData = $anbieterArray;
            }
            
            $loginStatus = $userArray ['status'];
           
            if ($anbieterArray ['last_login'] != '')
            {
              Model_DbTable_AnbieterData::saveAnbieter ('last_login', date ('d.m.Y H:i:s'), $anbieterID);
            }
            else
            {
              $loginStatus = $userArray ['status'];
              if ($loginStatus > 0)
              {
                if ($this->getRequest ()->getParam ('error') == 'login')
                {
                  $this->render ('index');
                  $this->render ('loginfailure');
                }
                
                $this->_redirect ('/login/index/index/error/changepassword/aid/' . $anbieterID . '/hash/' . $userArray ['hash']);
              }
            }
          }
          else
          {
            // kein Passwort
            $loginStatus = -2;
          }
        }
        else
        {
          // kein Username (Passwort wird nicht beachtet)
          $loginStatus = -1;
        }
        ////logDebug ("LoginStaus: $loginStatus", "goAction");
        if ($loginStatus < 1) // Login nicht ok
        {
          $this->view->setBasePath ('../application/views');
          $this->_helper->redirector->gotoUrl ('/login/index/index/error/login');
        }
        else
        {
          //TODO Entscheidung ob zum Userbereich oder Adminbereich geforwarded wird
          $this->_helper->redirector->gotoUrl ('/einfuehrung/index/index');
        }
      }
      else // user ist in der User-Tabelle und damit ein Admin-User
      {
        if (md5 ($vars ['password']) != $userArray ['password'])
        {
          $this->view->setBasePath ('../application/views');
          $this->_helper->redirector->gotoUrl ('/login/index/index/error/login');
        }
        $anbieterID = $userArray ['primaryAnbieterID'];
        if ($anbieterID > 0)
        {
          $anbieterArray = $anbieterModel->getAnbieter ($anbieterID);
          // userArray kompatibel umschreiben bzw. erweitern ...
          $userArray ['hash'] = $userArray ['userHash'];
          $userArray ['anbieterID'] = $anbieterID;
          $sessionNamespace = new Zend_Session_Namespace ();
          if (is_array ($userArray))
          {
            $sessionNamespace->userData = $userArray;
          }
          if (is_array ($anbieterArray))
          {
            $sessionNamespace->anbieterData = $anbieterArray;
            logDebug (print_r ($anbieterArray, true), "");
          }
          $this->_helper->redirector->gotoUrl ('/einfuehrung/index/index');
        }
        else
        {
          die ('Fehler bei der Zuordnung des Primäranbieters! System gestoppt!');
        }
      }
    }
  }

?>
