<?php
  /**
   * Hash-Überprüfung zur Authentifizierung z.B. bei Ajax-Anfragen
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Plugin_HashControl extends Zend_Controller_Plugin_Abstract
  {

    /**
     * @var object Userdaten
     */
    var $userData = NULL;


    /**
     * führt die Hashüberprüfung vor dem Dispatch aus und leitet entsprechend um bzw. weiter
     *      *
     *
     * @param Zend_Controller_Request_Abstract $request
     *
     * @return null
     */
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
      $config = Zend_Registry::get ('config');
      $sessionNamespace = new Zend_Session_Namespace ();
      $userData = $sessionNamespace->userData;
      $hashOK = TRUE;
      $config = Zend_Registry::get ('config');
      $params = $this->getRequest ()->getParams ();
      //logDebug (print_r ($params, true), "HashControl:preDispatch");
      $controller = $params ['controller'];
      // hier wird zunächst geprüft ob der Controller ein Ajax-Controller ist. Das ist dann der Fall wenn nach dem . im Controllernamen ein "ajax" steht
      $controllerNameArray = explode ('.', $controller);
      ////logDebug (print_r ($params, true), "");
      if ($controller == 'login.ajax' || $controller == 'testing')
      {
        return NULL;
      } // hash-prüfung nicht bei login-ajax-anfragen durchführen
      if (count ($controllerNameArray) > 1 && $hashOK)
      {
        if ($controllerNameArray [1] == 'ajax') // hash-Prüfung bei Ajax-Anfrage durchführen
        {
          $hashOK = array_key_exists ('hash', $params) && array_key_exists ('anbieterID', $params);
          $anbieterID = $params ['anbieterID'];
          $hash = $params ['hash'];
          $anbieterModel = new Model_DbTable_AnbieterData ();
          if ($anbieterID != NULL)
          {
            $anbieterDetails = $anbieterModel->getAnbieterDetails ($anbieterID);
          }
          $anbieterHash = $anbieterDetails ['ANBIETERHASH'];
          $hashOK = ($anbieterHash == $hash);
          if (!array_key_exists ('userStatus', $userData) || !$userData ['userStatus'] < 0)
          {
            if ($hashOK == FALSE || $anbieterID == NULL) // TODO Problemlösung für AnbieterID wegen VM Kundennummer (keine AnbieterID vorhanden)????
            {
             // die ('HashControl::Error #11401');
            }
            // wenn die AJAX Anfrage von einem Nicht-Premium-Kunden kommt und NICHT die Stammdaten betrifft -> error
            if ($params ['module'] != 'stammdaten' && $params ['module'] != 'einfuehrung' && !$anbieterDetails ['PREMIUMLEVEL'] > 0)
            {
              logError ("Anfrage nicht von Stammdaten und Anbieter kein Premiumanbieter. Params: " . print_r ($params, true), "HashControl");
              die ('no premium user');
            }
          }
        }
      }
      if ($params ['module'] == "admin")
      {
        $allowedUserString = $config->admin->allowedUser;
        $allowedUserArray = explode (',', $allowedUserString);
        $sessionNamespace = new Zend_Session_Namespace ();
        $userData = $sessionNamespace->userData;
        logDebug (print_r ($userData, true), "userData");
        $userIsAdmin = false;
        if (is_array ($userData) && array_key_exists ('userHash', $userData))
        {
          $userHash = $userData ['userHash'];
          $userModel = new Model_DbTable_UserData();
          $userByHash = $userModel->getUserByHash ($userHash);
          logDebug (print_r ($userByHash, true), "userData");
          $userIsAdmin = ($userByHash['userHash'] == $userHash);
        }
        if (!$userIsAdmin) {
          die ('Admin access is not allowed for you!!!');
        }
      }
    }
  }

?>
