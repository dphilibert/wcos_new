<?php

  /**
   * Datenbank-Model für Jobs
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   * @todo re-engineering
   */

class Model_DbTable_JobsData extends Zend_Db_Table_Abstract
{

  public function init ()
  {
  }

  /**
   * liefert eine Liste mit Jobs des anbieters
   *
   * @param int $anbieterID anbieterID
   * @return array
   */

  public function getJobs ($anbieterID) // Job LISTE
  {
    $db = Zend_Registry::get ('db');  
    $select = $db->select();
    $select->from(array('j' => 'jobs'));
    $select->where ("j.anbieterID = ?", $anbieterID);
    $select->where ("j.status = ?", 1);

    $result = $select->query ();
    $data = $result->fetchAll ();
    array_walk_recursive ($data, 'utfDecode');
    return $data;
  }

/**
 * liefer das Dataset eines Jobs
 *
 * @param int $jobID jobID
 * @return array
 */

  public function getJob ($jobID) // ein einzelner Job
  {
    $db = Zend_Registry::get ('db');  
    $select = $db->select();
    $select->from(array('j' => 'jobs'));
    $select->where ("j.jobID = ?", $jobID);
    $select->where ("j.status = ?", 1);

    $result = $select->query ();
    $data = $result->fetchAll ();
    array_walk_recursive ($data, 'utfDecode');
    return $data;
  }


  /**
   * speichert einen Job
   *
   * @param string $field DB-Feld
   * @param string $value DB-Wert
   * @param int $ID jobID
   * @return int 0=speichern erfolgreich, 2=Fehler beim speichern
   */

  public function saveJob ($field = NULL, $value, $ID)
  {
    $db = Zend_Registry::get ('db');
    if ($field != NULL)
    {
      $tableName = "jobs";
      $whereCond = "jobID = $ID";
    }
    $data = array ($field => $value);
    $data ['status'] = 1;
    try {
         $n = $db->update ($tableName, $data, $whereCond);
        } catch (Exception $e)
          {
            logError ($e->getMessage (), "JobsAjaxController::saveJob");
            return 2; // Return-Code ==> Fehler beim Speichern
          }
    return 0; // Return-Code ==> Speichern erfolgreich
  }


  /**
   * als gelöschte Jobs markierte Jobs endgültig löschen
   * @return void
   */

  public function clearJobsTable ()
  {
    $db = Zend_Registry::get ('db');
    try
    {
      // nicht vollstaendig angelegte neue jobs raus!
      $db->query ("DELETE FROM jobs WHERE status=0");
      // geloeschte jobs raus!
      $db->query ("DELETE FROM jobs WHERE status=-1");
    } catch (Exception $e)
      {
        logError ($e->getMessage (), "AjaxController::clearWhitepaperTable");
      }
  }

  /**
   * Job "hart" aus der DB löschen
   * @param int $ID jobID
   */

  public function hardDelJob ($ID)
  {
    $db = Zend_Registry::get ('db');
    try
    {
      $db->query ("DELETE FROM jobs WHERE jobID=$ID");
    } catch (Exception $e)
      {
        logError ($e->getMessage (), "AjaxController::hardDelJob");
      }
  }


  /**
   * neuen, leerenJob-Datensatz erzeugen
   *
   * @param int $anbieterID anbieterID
   * @return int lastInsertID die ID des eingefügten Datensatzes
   */

  public function newJob ($anbieterID)
  {
    $db = Zend_Registry::get ('db');
    try
    {
      $db->query ("INSERT INTO jobs (anbieterID, status) VALUES ($anbieterID, 0)");
    } catch (Exception $e)
      {
        logError ($e->getMessage (), "JobsAjaxController::neJob");
      }
   return $db->lastInsertId ();
  } 










}

?>
