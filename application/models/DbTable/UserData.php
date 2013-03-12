<?php
/**
 * Datenbank-Model für User
 *
 * @author Thomas Grahammer
 * @version $id$
 *
 */
  class Model_DbTable_UserData extends Zend_Db_Table_Abstract
  {

    var $newUserID = NULL,
    $newUserDetailID = NULL;

    /**
     *
     * ininitales Init
     *
     * @return void
     *
     */
    public function init ()
    {
    }

    /**
     * prüft die Existenz eines Usernamens in der User-Tabelle
     *
     * @param string $username
     *
     * @return bool true=Username existiert, false=Username existiert nicht
     */
    public function userExists ($username)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ()
      ->from (array('u' => 'user'))
      ->where ('u.username = ?', $username);
      try
      {
        $result = $select->query ();
        $data = $result->fetch ();
      } catch (Zend_Exception $e)
      {
        logError (print_r ($e, true), "user::userExists");
      }
      if (count ($data) > 0)
      {
        return $data;
      }
      return false;
    }


    /**
     * liefer den User-Datensatz anhand der UserID
     *
     * @param int $userID
     *
     * @return bool | array false=Fehler, array=User-Datensatz
     */
    public function getUserInfo ($userID)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ()
      ->from (array('u' => 'user'))
      ->where ('u.userID = ?', $userID);
      try
      {
        $result = $select->query ();
        $data = $result->fetch ();
      } catch (Zend_Exception $e)
      {
        logError (print_r ($e, true), "user::getUserInfo");
      }
      if (count ($data) > 0)
      {
        return $data;
      }
      return false;
    }

    /**
     * liefer den User-Datensatz anhand des User-Hashcodes
     *
     * @param string hash User-Hashcode
     *
     * @return bool | array false=Fehler, array=User-Datensatz
     */
    public function getUserByHash ($hash)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ()
      ->from (array('u' => 'user'))
      ->where ('u.userHash = ?', $hash);
      try
      {
        $result = $select->query ();
        $data = $result->fetch ();
      } catch (Zend_Exception $e)
      {
        logError (print_r ($e, true), "user::getUserInfo");
      }
      if (count ($data) > 0)
      {
        return $data;
      }
      return false;
    }

    // alle nachfolgenden Funcs sind depricated ...

/*
    public function getUserData ($userID)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ()
      ->from (array('u' => 'user'))
      ->join (array('ud' => 'userDetails'),
        'u.userDetailID = ud.userDetail_ID')
      ->where ('u.userID = ?', $userID);
      $result = $select->query ();
      $data = $result->fetch ();
      return $data;
    }

    public function getUserList ($token = NULL)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('u' => 'user'));
      $select->join (array('ud' => 'userDetails'),
        'u.userDetailID = ud.userDetail_ID');
      if ($token != NULL && $token != 'undefined')
      {
        $select->where ("u.username like '%$token%'");
      }
      $select->where ("u.userStatus >= 0");
      $result = $select->query ();
      $data = $result->fetchAll ();
      return $data;
    }

    public function getUserDetails ($userID)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('u' => 'user'));
      $select->join (array('ud' => 'userDetails'),
        'u.userDetailID = ud.userDetail_ID');
      $select->where ("u.userID = ?", $userID);
      $result = $select->query ();
      $data = $result->fetchAll ();
      return $data;
    }

    public function findUser ($searchField, $searchValue)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('u' => 'user'));
      $select->join (array('ud' => 'userDetails'),
        'u.userDetailID = ud.userDetail_ID');
      $select->where (" $searchField = ?", $searchValue);
      $result = $select->query ();
      $data = $result->fetchAll ();
      return $data;
    }


    public function saveUser ($userID, $userDetailID, $feldName = NULL, $feldWert)
    {
      $db = Zend_Registry::get ('db');
      if ($feldName != NULL) // nur ein Feld des Users speichern
      {
        $tableName = "user";
        $whereCond = "userID = $userID";
        if ($feldName != 'username')
        {
          $tableName = "userDetails";
          $whereCond = "userDetail_ID = $userDetailID";
        }
        $data = array($feldName => $feldWert);
        try
        {
          $n = $db->update ($tableName, $data, $whereCond);
        } catch (Exception $e)
        {
          logError ($e->getMessage (), "AjaxController::saveUser");
          return 2; // Return-Code ==> Fehler beim Speichern
        }
      }
      else // alle Felder speichern
      {
      }
      return 0; // Return-Code ==> Speichern erfolgreich
    }


    // legt einen neuen Benutzer an (INSERT) und setzt $this->newUserID und $this->newUserDetailID
    public function createUser ()
    {
      $db = Zend_Registry::get ('db');
      $sql = "INSERT INTO userDetails (creation_date) values ('2010-01-01')";
      $db->query ($sql);
      $this->newUserDetailID = $db->lastInsertId ();
      $userHash = md5 (microtime ());
      $sql = "INSERT INTO user (userDetailID, userHash, userStatus) VALUES ($this->newUserDetailID, '$userHash', 1)";
      $db->query ($sql);
      $this->newUserID = $db->lastInsertId ();
      $retArray ['newUserID'] = $this->newUserID;
      $retArray ['newUserDetailID'] = $this->newUserDetailID;
      $retArray ['userHash'] = $userHash;
      return $retArray;
    }


    // delete, lock und unlock wurden als eigene Funktionen eingebaut (und nicht als
    // Ableitung von saveUser), damit wir eigene Log-Moeglichkeiten haben und ggf. unterschiedliche
    // Tabellen zusaetzlich ansprechen koennen.
    // deleteUser - markiert einen User als geloescht (userStatus = -1)
    public function deleteUser ($userID)
    {
      $db = Zend_Registry::get ('db');
      try
      {
        $db->update ('user', array('userStatus' => '-1'), 'userID = ' . $userID);
      } catch (Exception $e)
      {
        // Fehler beim Loeschvorgang
        logError ($e->getMessage (), "AjaxController::deleteUser");
        return 2;
      }
      return 0; // loeschen (bzw. als geloescht markieren) erfolgreich
    }

    // fuehrt ein Hard-Del (echtes delete) fuer einen User durch
    public function hardDelUser ($userID)
    {
      $db = Zend_Registry::get ('db');
      try
      {
        // bevor geloescht werden kann, erst die DetailID sichern um den Datensatz dann
        // danach loeschen zu koennen
        $select = $db->select ();
        $select->from (array('u' => 'user'));
        $select->where (" userID = ?", $userID);
        $result = $select->query ();
        $data = $result->fetch ();
        $userDetailID = $data ['userDetailID'];
        $db->query ("DELETE FROM user WHERE userID=$userID");
        $db->query ("DELETE FROM userDetails WHERE userDetail_ID=$userDetailID");
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "AjaxController::hardDelUser");
      }
    }

    // lockUser - markiert einen Benutzer als gesperrt
    public function lockUser ($userID)
    {
      $db = Zend_Registry::get ('db');
      try
      {
        $db->update ('user', array('userStatus' => '0'), 'userID = ' . $userID);
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "AjaxController::lockUser");
        return 2;
      }
      return 0; // locken erfolgreich
    }

    // unlockUser - markiert einen Benutzer als un-gesperrt
    public function unlockUser ($userID)
    {
      $db = Zend_Registry::get ('db');
      try
      {
        $db->update ('user', array('userStatus' => '1'), 'userID = ' . $userID);
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "AjaxController::unlockUser");
        return 2;
      }
      return 0; // unlock erfolgreich
    }

    public function addUser2Anbieter ($userID, $anbieterID)
    {
      try
      {
        $db = Zend_Registry::get ('db');
        $sql = "INSERT INTO user2anbieter (anbieterID, userID) values ($anbieterID, $userID)";
        $db->query ($sql);
      } catch (Zend_Exception $ze)
      {
        logError ($ze->getMessage (), "UserData::addUser2Anbieter");
      }
    }

    public function delUser2Anbieter ($userID, $anbieterID)
    {
      try
      {
        $db = Zend_Registry::get ('db');
        $sql = "DELETE FROM user2anbieter WHERE userID = $userID AND anbieterID = $anbieterID";
        $db->query ($sql);
      } catch (Zend_Exception $ze)
      {
        logError ($ze->getMessage (), "UserData::delUser2Anbieter");
      }
    }

*/
  }

?>
