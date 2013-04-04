<?php

  /**
   * Module Whitepaper - Uploads
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Whitepaper_UploadController extends Zend_Controller_Action
  {


    /**
     * führt den Upload durch und gibt das Ergebnis als Json aus
     *
     * @return void
     */
    public function indexAction ()
    {      
      $config = Zend_Registry::get ('config');
      $allowedExtensions = explode (",", $config->uploads->allowedExtensions);
      $videoExtensions = explode (",", $config->uploads->videoExtensions);     
      $sizeLimit = 10 * 1024 * 1024 * 1024;
      $uploader = new qqFileUploader($allowedExtensions, $sizeLimit, $videoExtensions);
      
      $mediaID = md5 (uniqid ());
      $result = $uploader->handleUpload ('uploads/', $mediaID);
                  
      $this->_helper->json->sendJson ($result);
    }
  }

?>
