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
      //logDebug (print_r ($_POST, true));
      $vars ['username'] = base64_decode ($vars ['username']);
      $vars ['password'] = base64_decode ($vars ['password']);
      // prüfen ob username in User-Tabelle (dann wäre es ein Admin-User)
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
            //logDebug (print_r ($userArray, true), "");
            $userArray ['userDaten'] = $soap_client->get_userdaten ($userArray ['hash']);
            logDebug (print_r ($userArray, true), "foo");
            $userID = $userArray ['userDaten'] ['user_id'];
            // TODO umbauen auf VM-Kundennummer-Abfrage
            // wir bekommen einen User aus dem ZBVS mit der Anbieter-Kundennummer (firmaKundennumme) des VM
            // damit holen wir uns die Anbieter-Daten aus der Anbieter-Tabelle (respektive VM)
            $firmaKundennummer = $userArray ['userDaten'] ['firmaKundennummer'];
            //logDebug ($firmaKundennummer, "Kundennummer");
            if ($userID > 0)
            {
              $anbieterArray = $anbieterModel->getAnbieterByKundennummer ($firmaKundennummer);
            }
            // if ($userID > 0) $anbieterArray = $anbieterModel->getAnbieterByUser ($userID);
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
            //$global = new Plugin_Global ();
            //$global->switchAnbieter ();
            $loginStatus = $userArray ['status'];
            // TODO Abfrage via ZBVS-Datensatz ob User für WCOS freigegeben und wenn ja, für welches Systen (später)
            // falls nicht, raus mit ihm
            // TODO Überprüfung ob anbieter::lastLogin-Feld ausgefüllt. Wenn ja, war der User bereits schoneinmal eingeloggt.
            // in diesem Fall: lastLogin in die Tabelle schreiben
            // andernfalls: Anzeige Passwort-ändern seite. Anschliessend neues Login
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
                //$this->_redirect ('/login/changepassword'); // DEPRICATED!!!
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
