<?php
  /**
   * Datenbank-Model Media
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   *
   */
  class Model_DbTable_MediaData extends Zend_Db_Table_Abstract
  {

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
     * liefert eine Liste von Medien
     *
     * @param int $anbieterID anbieterID
     * @param mixed $typSelect Medien-Typen
     *
     * @return mixed
     */
    public function getMedienList ($anbieterID, $typSelect = NULL)
    {
      $orCond = NULL;
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('m' => 'media'),
        array('mediatypdesc' => 'mt.beschreibung',
          'mediadesc' => 'm.beschreibung',
          '*' => 'm.*'));
      $select->join (array('mt' => 'mediatypen'),
        'mt.mediatyp = m.mediatyp');
      $select->where ("m.status > 0");
      $select->where ("m.anbieterID = ?", $anbieterID);
      $select->order ("position ASC");
      if ($typSelect != NULL)
      {
        if (is_array ($typSelect)) // falls der Filter als Array kommt
        {
          $i = 0;
          foreach ($typSelect as $key => $value)
          {
            $i++;
            $orCond .= "m.mediatyp = '$value'";
            if ($i < count ($typSelect)) {
              $orCond .= " OR ";
            }
          }
          $select->where ("(" . $orCond . ")");
        }
        else // oder als Einzelwert
        {
          $select->where ("m.mediatyp = ?", $typSelect);
        }
      }
      $result = $select->query ();
      $data = $result->fetchAll ();
      return $data;
    }

    /**
     * liefert eine Liste von Medien-Typen
     *
     * @return mixed
     */
    public function getMediaTypen ()
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('mt' => 'mediatypen'));
      $result = $select->query ();
      $data = $result->fetchAll ();
      return $data;
    }

    /**
     * Lädt ein Medie-File hoch
     *
     * @todo re-engineering - das gehört hier (DbTable) nicht hin sondern in ein extra Model
     *
     * @param array $fileInfo Datei-Informationen
     * @param string $beschreibung Beschreibung
     * @param string $mediaTyp Medien-Typ
     * @param int $mediaID mediaID
     *
     * @return int
     * @throws Exception
     */
    public function uploadMediaFile ($fileInfo, $beschreibung, $mediaTyp, $mediaID)
    {
      $db = Zend_Registry::get ('db');
      $config = Zend_Registry::get ('config');
      $tableName = "media";
      $whereCond = "mediaID = $mediaID";
      ////logDebug ($beschreibung . "/" . $mediaTyp . "/".print_r ($fileInfo, true), "tgr");
      $tmpName = $fileInfo [tmp_name];
      switch ($fileInfo [type])
      {
        case 'text/plain' :
          $extension = 'txt';
          break;
        case 'image/jpeg' :
          $extension = 'jpg';
          break;
        case 'image/gif' :
          $extension = 'gif';
          break;
        default :
          $extension = 'unknown';
          break;
      }
      $data = array('mediatyp' => $mediaTyp,
        'beschreibung' => $beschreibung,
        'mediadatei' => $fileInfo [name],
        'mediaExtension' => $extension);
      $uploadDestination = $config->uploads->path;
      if (file_exists ($tmpName))
      {
        try
        {
          $newName = $mediaID . '.' . $extension;
          if (file_exists ($newName)) {
            @unlink ($newName);
          } // falls die Datei schon existiert - erst löschen
          if (@rename ($tmpName, $uploadDestination . '/' . $newName)) // ... dann rename von tmp-file auf newName
          {
            // Format pruefen und ggf. abaendern
            $_imgSize = getimagesize ($uploadDestination . '/' . $newName);
            $_imgWidth = $_imgSize [0];
            $_imgHeight = $_imgSize [1];
            $_maxWidth = 220;
            if (isset ($config->uploads->maxWidth)) {
              $_maxWidth = $config->uploads->maxWidth;
            }
            if ($_imgWidth > $_maxWidth)
            {
              $_newImgWidth = $_maxWidth;
              $_factor = $_imgWidth / $_maxWidth;
              $_newImgHeight = $_imgHeight / $_factor;
              $image = new SimpleImage (); // in library/SimpleImage.php
              $image->load ($uploadDestination . '/' . $newName);
              $image->resize ($_newImgWidth, $_newImgHeight);
              $image->save ($uploadDestination . '/' . $newName);
            }
          }
          else // rename fehlerhaft
          {
            logError ("rename Fehler", "MediaAjaxController::saveMediaFile");
            return 2;
          }
        } catch (Exception $e)
        {
          logError ($e->getMessage (), "MediaAjaxController::saveMediaFile");
          return 2; // Return-Code ==> Fehler beim Speichern
        }
      }
      else
      {
        throw new Exception ('Datei nicht gefunden!');
      }
      try
      {
        $n = $db->update ($tableName, $data, $whereCond);
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "MediaAjaxController::saveMediaFile");
        return 2; // Return-Code ==> Fehler beim Speichern
      }
      return 0; // Return-Code ==> Speichern erfolgreich
    }

    /**
     * speichert Medium
     *
     * @param string $field DB-Feld
     * @param string $value DB-Wert
     * @param int $mID mediaID
     *
     * @return int 0=speichern erfolgreich, 2=Fehler beim speichern
     */
    public function saveMedia ($field = NULL, $value, $mID)
    {
      ////logDebug ("feld: $field / val: $value", "$mID");
      $db = Zend_Registry::get ('db');
      if ($field != NULL)
      {
        $tableName = "media";
        $whereCond = "mediaID = $mID";
      }
      $data = array($field => $value);
      $data ['status'] = '1';
      try
      {
        $n = $db->update ($tableName, $data, $whereCond);
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "MediaAjaxController::saveMedia");
        logError (print_r ($whereCond, true), "MediaAjaxController::saveMedia");
        return 2; // Return-Code ==> Fehler beim Speichern
      }
      return 0; // Return-Code ==> Speichern erfolgreich
    }


    // loescht alle Eintraege mit status=0 (diese wurden von new erzeugt ohne zu speichern, also Abbruch bei new)
    /**
     * alle Zombie-Einträge in der Tabelle media löschen
     *
     * @return void
     *
     */
    public function clearMediaTable ()
    {
      $db = Zend_Registry::get ('db');
      try
      {
        // nicht vollstaendig angelegte neue Medien raus!
        $db->query ("DELETE FROM media WHERE status=0");
        // geloeschte Medien raus!
        $db->query ("DELETE FROM media WHERE status=-1");
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "AjaxController::clearMediaTable");
      }
    }

    /**
     * markiert das Medium als gelöscht
     *
     * @param int $mID mediaID
     *
     * @return int 0=löschen erfogreich, 2=Fehler beim löschen
     */
    public function delMediaFile ($mID)
    {
      $config = Zend_Registry::get ('config');
      $db = Zend_Registry::get ('db');
      $tableName = "media";
      $whereCond = "mediaID = $mID";
      $data = array("status" => "-1");
      $uploadDestination = $config->uploads->path;
      try
      {
        $n = $db->update ($tableName, $data, $whereCond);
        @unlink ($uploadDestination . '/' . $mID . '.*');
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "MediaAjaxController::delMediaFile");
        return 2; // Return-Code ==> Fehler beim löschen
      }
      return 0; // Return-Code ==> löschen erfolgreich
    }

    /**
     * erzeugt neuen DB-Eintrag für ein Medium (new)
     *
     * @param int $anbieterID anbieterID
     *
     * @return int $id die ID des neuen Eintrags
     */
    public function newMediaFile ($anbieterID)
    {
      //logDebug ('new MediaFile', "tgr");
      $db = Zend_Registry::get ('db');
      try
      {
        $db->query ("INSERT INTO media (anbieterID, status) VALUES ($anbieterID, 0)");
      } catch (Exception $e)
      {
        logError ($e->getMessage (), "MediaAjaxController::newMediaFile");
      }
      return $db->lastInsertId ();
    }

    /**
     * liefert einen Media-Eintrag
     *
     * @param int $mID mediaID
     *
     * @return array
     */
    public function getMedia ($mID)
    {
      $db = Zend_Registry::get ('db');
      $select = $db->select ();
      $select->from (array('m' => 'media'));
      $select->where ("m.mediaID = $mID");
      $result = $select->query ();
      $data = $result->fetchAll ();
      return $data;
    }

    /**
     * liefert alle Medien zu einem Anbieter
     *
     * @param int $aID anbieterID
     *
     * @depricated null $minimumTyp
     * @return array
     */
    public function getAllMedia ($aID, $minimumTyp = NULL)
    {
      $minimumTypWhere = '';
      if ($minimumTyp != NULL)
      {
        $minimumTypWhere = "and m.mediatyp >= $minimumTyp";
      }
      $db = Zend_Registry::get ('db');
      $select = $db->select ("mt.beschreibung as mediatypdesc, *");
      $select->from (array('m' => 'media'));
      $select->join (array('mt' => 'mediatypen'),
        'mt.mediatyp = m.mediatyp');
      $select->where ("m.anbieterID = ?", $aID);
      $select->where ("m.status = ?", 1);
      $select->order ("position ASC");
      $result = $select->query ();
      $data = $result->fetchAll ();
      return $data;
    }
  }

?>
