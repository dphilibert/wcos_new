<?php

class Model_DbTable_WhitepaperData extends Zend_Db_Table_Abstract
{

  var $vorname = NULL;

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
   * liefer eine Liste mit Whitepapern zu einem Anbieter
   *
   * @param int $anbieterID anbieterID
   * @return mixed
   */
  public function getWhitepaperList ($anbieterID = NULL)
  {
    $db = Zend_Registry::get ('db'); 
    $db->query ("set character set utf8"); 
    $select = $db->select();
    $select->from (array ('w' => 'whitepaper'));
    $select->where ("w.whitepaper_status >= 0 AND w.whitepaper_anbieterID = ?", $anbieterID);
    $result = $select->query ();
    $data = $result->fetchAll ();
    return $data;
  }

  /**
   * liefert einen Whitepaper-Datensatz
   *
   * @param id $whitepaperID whitepaperID
   * @return mixed
   */
  public function getWhitepaper ($whitepaperID = NULL)
  {
    $db = Zend_Registry::get ('db'); 
    $db->query ("set character set utf8"); 
    $select = $db->select();
    $select->from (array ('w' => 'whitepaper'));
    $select->where ("w.whitepaper_status >= 0 AND w.whitepaperID = ?", $whitepaperID);
    $result = $select->query ();
    $data = $result->fetchAll ();
    return $data;
  }

  /**
   *
   * @param null $field
   * @param $value
   * @param $ID
   * @return int
   */

  public function saveWhitepaper ($field = NULL, $value, $ID)
  {    
    $db = Zend_Registry::get ('db');
    if ($field != NULL)
    {
      $tableName = "whitepaper";
      $whereCond = "whitepaperID = $ID";
    }
    $data = array ($field => $value);
    $data ['whitepaper_status'] = 1;
    try {
         $n = $db->update ($tableName, $data, $whereCond);
        } catch (Exception $e)
          {
            logError ($e->getMessage (), "WhitepaperAjaxController::saveWhitepaper");
            logError ($tableName." / ". $data." / ".$whereCond, "");
            return 2; // Return-Code ==> Fehler beim Speichern
          }
    return 0; // Return-Code ==> Speichern erfolgreich
  }


  public function clearWhitepaperTable ()
  {
    $db = Zend_Registry::get ('db');
    try
    {
      // nicht vollstaendig angelegte neue Whitepaper raus!
      $db->query ("DELETE FROM whitepaper WHERE whitepaper_status=0");
      // geloeschte Whitepaper raus!
      $db->query ("DELETE FROM whitepaper WHERE whitepaper_status=-1");
    } catch (Exception $e)
      {
        logError ($e->getMessage (), "AjaxController::clearWhitepaperTable");
      }
  }


  public function newWhitepaper ($anbieterID)
  {
    $db = Zend_Registry::get ('db');
    try
    {
      $db->query ("INSERT INTO whitepaper (whitepaper_anbieterID, whitepaper_status) VALUES ($anbieterID, 0)");
    } catch (Exception $e)
      {
        logError ($e->getMessage (), "MediaAjaxController::newWhitepaper");
      }
   return $db->lastInsertId ();
  } 
  
  public function lockWhitepaper ($ID, $hashCode)
  {
    $db = Zend_Registry::get ('db');   
    try {
         $n = $db->update ("whitepaper", array ('whitepaper_freigabe_hash' => $hashCode), "whitepaperID = $ID");
        } catch (Exception $e)
          {
            logError ($e->getMessage (), "WhitepaperAjaxController::lockWhitepaper");
            return 2; 
          }
    return 0; 
  }
  
  public function unlockWhitepaper ($hashCode)
  {
    $db = Zend_Registry::get ('db');
    $db->getProfiler()->setEnabled(true);
    $data = array ($field => $value);
    try {
          $n = $db->update ("whitepaper", array ('whitepaper_freigabe_hash' => ''), "whitepaper_freigabe_hash = '$hashCode'");

        } catch (Exception $e)
          {
            logError ($e->getMessage (), "WhitepaperAjaxController::unlockWhitepaper");
            $profiler = $db->getProfiler();
            $foo = $profiler->getLastQueryProfile();
            //logDebug (print_r ($foo, true), "Model_DbTable_Whitepaper::unlockWhitepaper");
            return 2; // Return-Code ==> Fehler beim Speichern
          }
    return 0; // Return-Code ==> Speichern erfolgreich
  }
  

  public function hardDelWhitepaper ($whitepaper_id)
  {
    $db = Zend_Registry::get ('db');     
    $db->delete ('whitepaper', 'whitepaperID = '.$whitepaper_id);
  }        
  
}

