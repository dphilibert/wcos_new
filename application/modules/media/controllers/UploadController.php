<?php

/**
 * Upload für das Modul Medien
 *
 * @author Thomas Grahammer
 * @version $id$
 */
class Media_UploadController extends Zend_Controller_Action
{

  public function init ()
  {
  }

/**
 * führt den Upload durch und liefert Mediendaten als json zurück
 *
 * @return void
 *
 */
  public function indexAction ()
  {   
   $config = Zend_Registry::get ('config');
   $allowedExtensions = explode (",", $config->uploads->allowedExtensions);
   $videoExtensions = explode (",", $config->uploads->videoExtensions);
   // max file size in bytes
   $sizeLimit = 10 * 1024 * 1024 * 1024;
   $uploader = new qqFileUploader($allowedExtensions, $sizeLimit, $videoExtensions);
   $mediaID = $this->getRequest ()->getParam ('mediaID');
   $result = $uploader->handleUpload('uploads/', $mediaID);   
   $this->_helper->json->sendJson ($result);
  }


}

?>
