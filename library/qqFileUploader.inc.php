<?php
  /**
   * handle via Ajax
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class qqUploadedFileXhr
  {
    /**
     * speichert die Datei in dem angegeben Pfad
     *
     * @return boolean TRUE bei Erfolg
     */
    function save ($path)
    {
      $input = fopen ("php://input", "r");
      $temp = tmpfile ();
      $realSize = stream_copy_to_stream ($input, $temp);
      fclose ($input);
      if ($realSize != $this->getSize ())
      {
        return false;
      }
      $target = fopen ($path, "w+");
      fseek ($temp, 0, SEEK_SET);
      stream_copy_to_stream ($temp, $target);
      fclose ($target);
      return true;
    }

    /**
     * liefert den Namen der Datei
     *
     * @return mixed Dateiname
     */
    function getName ()
    {
      return $_GET ['qqfile'];
    }

    /**
     * liefert die Größe der Datei
     *
     * @return int Dateigröße
     * @throws Exception
     *
     */
    function getSize ()
    {
      if (isset ($_SERVER ["CONTENT_LENGTH"]))
      {
        return (int)$_SERVER ["CONTENT_LENGTH"];
      }
      else
      {
        throw new Exception ('Getting content length is not supported.');
      }
    }

    /**
     * speichert ein Thumbnail
     *
     * @param $uploadDirectory Upload-Directory
     * @param $filename Dateiname
     *
     * @return bool TRUE bei Erfolg
     */
    function saveThumbnail ($uploadDirectory, $filename)
    {
      $thumbnailDirectory = $uploadDirectory . '/thumbnails';
      if (!is_dir ($thumbnailDirectory)) {
        return false;
      }
      $config = Zend_Registry::get ('config');
      // Format pruefen und ggf. abaendern
      $_imgSize = getimagesize ($uploadDirectory . '/' . $filename);
      $_imgWidth = $_imgSize [0];
      $_imgHeight = $_imgSize [1];
      $_newImgWidth = $config->uploads->thumbs->width;
      $_factor = $_imgWidth / $_newImgWidth;
      $_newImgHeight = $_imgHeight / $_factor;
      $image = new SimpleImage (); // in library/SimpleImage.php
      $image->load ($uploadDirectory . '/' . $filename);
      $image->resize ($_newImgWidth, $_newImgHeight);
      $image->save ($thumbnailDirectory . '/' . $filename);
    }

    /**
     * formatiert das Image
     *
     * @param $uploadDirectory Upload-Directory
     * @param $filename Dateiname
     */
    function formatImage ($uploadDirectory, $filename)
    {
      $config = Zend_Registry::get ('config');
      // Format pruefen und ggf. abaendern
      $_imgSize = getimagesize ($uploadDirectory . $filename);
      $_imgWidth = $_imgSize [0];
      $_imgHeight = $_imgSize [1];
      $_newImgWidth = $config->uploads->maxWidth;
      $_factor = $_imgWidth / $_newImgWidth;
      $_newImgHeight = $_imgHeight / $_factor;
      $image = new SimpleImage (); // in library/SimpleImage.php
      $image->load ($uploadDirectory . $filename);
      //logDebug ($_newImgHeight." / ".$_newImgWidth, "");
      $image->resize ($_newImgWidth, $_newImgHeight);
      $image->save ($uploadDirectory . $filename);
    }
  }

  /**
   * Handle die Upload via POST ($_FILES)
   *
   * @author Thomas Grahammer
   * @version $id$
   */
  class qqUploadedFileForm
  {

    /**
     * speichert die Datei
     *
     * @param $path Pfad
     *
     * @return bool TRUE bei Erolg
     */
    function save ($path)
    {
      if (!move_uploaded_file ($_FILES ['qqfile'] ['tmp_name'], $path))
      {
        return false;
      }
      return true;
    }

    /**
     * liefert den Namen der Datei
     *
     * @return mixed Dateiname
     */
    function getName ()
    {
      return $_FILES ['qqfile'] ['name'];
    }

    /**
     * liefert die Größe der Datei
     *
     * @return int Dateigröße
     *
     */
    function getSize ()
    {
      return $_FILES ['qqfile'] ['size'];
    }
  }


  /**
   * Uploader Klasse
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class qqFileUploader
  {
    /**
     * erlaubte Extensions
     *
     * @var array Extensions
     */
    private $allowedExtensions = array();

    /**
     * Video Extensions
     *
     * @var array Extensions
     */
    private $videoExtensions = NULL;

    /**
     * Größenlimit
     *
     * @var int Limit in byte
     */
    private $sizeLimit = 10737418240;

    /**
     * Datei-Handle
     *
     * @var bool|qqUploadedFileForm|qqUploadedFileXhr
     */
    private $file;

    /**
     * Konstruktor
     *
     * @param array $allowedExtensions erlaubte Extensions
     * @param int $sizeLimit Größenlimit
     * @param null $videoExtensions Video-Extensions
     *
     * @return void
     */
    function __construct (array $allowedExtensions = array(), $sizeLimit = 10737418240, $videoExtensions = NULL)
    {
      $allowedExtensions = array_map ("strtolower", $allowedExtensions);
      $videoExtensions = array_map ("strtolower", $videoExtensions);
      $this->allowedExtensions = $allowedExtensions;
      $this->videoExtensions = $videoExtensions;
      $this->sizeLimit = $sizeLimit;
      $this->checkServerSettings ();
      if (isset ($_GET ['qqfile']))
      {
        $this->file = new qqUploadedFileXhr ();
      }
      elseif (isset ($_FILES['qqfile']))
      {
        $this->file = new qqUploadedFileForm();
      }
      else
      {
        $this->file = false;
      }
    }


    /**
     * prüft die Server-Settings
     *
     * @return void
     */
    private function checkServerSettings ()
    {
      /*
              $postSize = $this->toBytes(ini_get('post_max_size'));
              $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));
              if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
                  $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
                  die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
              }
      */
    }

    /**
     * wandelt einen Human-Readable-String in eine Byte-Anzahl um
     *
     * @param $str Human-Readable-String
     *
     * @return int|string bytes
     */
    private function toBytes ($str)
    {
      $val = trim ($str);
      $last = strtolower ($str [strlen ($str) - 1]);
      switch ($last)
      {
        case 'g':
          $val *= 1024;
        case 'm':
          $val *= 1024;
        case 'k':
          $val *= 1024;
      }
      return $val;
    }

    /**
     * Upload-Handler
     *
     * @return array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload ($uploadDirectory, $mediaID, $replaceOldFile = FALSE, $fileEdit = FALSE)
    {
      if (!is_writable ($uploadDirectory))
      {
        return array('error' => "Server error. Upload directory isn't writable.");
      }
      if (!$this->file)
      {
        return array('error' => 'No files were uploaded.');
      }
      $size = $this->file->getSize ();
      if ($size == 0)
      {
        return array('error' => 'File is empty');
      }
      if ($size > $this->sizeLimit)
      {
        return array('error' => 'File is too large');
      }
      $pathinfo = pathinfo ($this->file->getName ());
//    $filename = $pathinfo ['filename'];
      // $filename = md5(uniqid());
      $filename = $mediaID;
      $ext = $pathinfo ['extension'];
      if ($this->allowedExtensions && !in_array (strtolower ($ext), $this->allowedExtensions))
      {
        $these = implode (', ', $this->allowedExtensions);
        return array('error' => 'File has an invalid extension, it should be one of ' . $these . '.');
      }
      $isVideo = in_array (strtolower ($ext), $this->videoExtensions);
      /*
          if (!$replaceOldFile)
          {
            /// don't overwrite previous files that were uploaded
            while (file_exists ($uploadDirectory . $filename . '.' . $ext))
            {
              $filename .= rand(10, 99);
            }
          }
      */
      $imgFullPath = getcwd () . '/' . $uploadDirectory . $filename . '.' . $ext;
      $unlinkStatus = @unlink ($imgFullPath);
      if ($this->file->save ($uploadDirectory . $filename . '.' . $ext) && !$isVideo) // Upload ist ein Bild
      {
        $result = array('success' => true, 'MEDIAEXTENSION' => $ext, "MEDIATYP" => "bild", "FILENAME" => $filename);
        $this->file->saveThumbnail ($uploadDirectory, $filename . '.' . $ext);
        $this->file->formatImage ($uploadDirectory, $filename . '.' . $ext);
      }
      if ($isVideo) // Upload ist ein Video
      {
        $result = array('success' => true, 'MEDIAEXTENSION' => $ext, "MEDIATYP" => "video", "FILENAME" => $filename);
      }
      if (strtolower ($ext) == 'pdf') // Upload ist ein PDF
      {
        $result = array('success' => true, 'MEDIAEXTENSION' => $ext, "MEDIATYP" => "pdf", "FILENAME" => $filename);
      }
      return $result;
    }
  }

?>
