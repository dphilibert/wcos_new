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
      // list of valid extensions, ex. array("jpeg", "xml", "bmp")
      //$allowedExtensions = array();
      $config = Zend_Registry::get ('config');
      $allowedExtensions = explode (",", $config->uploads->allowedExtensions);
      $videoExtensions = explode (",", $config->uploads->videoExtensions);
      // max file size in bytes
      $sizeLimit = 10 * 1024 * 1024 * 1024;
      $uploader = new qqFileUploader($allowedExtensions, $sizeLimit, $videoExtensions);
      //$mediaID = $this->getRequest ()->getParam ('mediaID');
      $mediaID = md5 (uniqid ());
      $result = $uploader->handleUpload ('uploads/', $mediaID);
      //logDebug (print_r ($result, true), "uploader");
      $this->_helper->json->sendJson ($result);
    }
  }

?>
