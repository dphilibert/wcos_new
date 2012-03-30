<?php
  /**
   * API zur Anbindung des Verlagsmanagers (VM)
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   *
   */
  class Model_ExtAPI_Verlagsmanager
  {

    var $client = NULL,
    $queryKeyLines = NULL;

    /**
     * stellt die Verbindung zum VM-System her
     *
     * @return void
     */
    public function __construct ()
    {
      $config = Zend_Registry::get ('config');
      $vmURL = "http://" . $config->vm->ip . ":" . $config->vm->port . "/4DWSDL";
      $this->client = new SoapClient($vmURL, array('login' => $config->vm->username, 'password' => $config->vm->password));
    }

    /**
     * setzt die Suchfelder und die Suchbegriffe für die Suchfunktionen
     *
     * @param $field
     * @param $value
     *
     * @return void
     */
    public function selectFields ($field, $value)
    {
      $this->queryKeyLines .= '<' . $field . '>' . $value . '</' . $field . '>';
    }

    /**
     * baut das XML für den Request an den VM zusammen
     *
     * @return string
     */
    public function buildQuery ()
    {
      $view = new Zend_View ();
      $view->setScriptPath (getcwd () . "/../application/models/ExtAPI/");
      $view->queryKeyLines = $this->queryKeyLines;
      $options = '<?xml version="1.0" encoding="ISO-8859-1"?>';
      $options .= $view->render ('query.xml');
      return $options;
    }


    /**
     * holt einen Adressdatensatz aus dem VM
     *
     * @param string $phrase Suchbegriff
     * @param string $xml
     *
     * @return mixed
     */
    public function searchAddress ($phrase = NULL)
    {
      if ($phrase != NULL)
      {
        $this->selectFields ('keyName', $phrase);
        $query = $this->buildQuery ();
        $result = $this->client->ws_find_address (100, $query);
        $xml = $result ['GP_resultList'];
        $rowCount = $result ['rowCount'];
        $xmlObj = simplexml_load_string ($xml);
        return $xmlObj;
      }
    }

    public function searchAddressByKundennummer ($customerNo = NULL)
    {
      if ($customerNo != NULL)
      {
        $this->selectFields ('customerNo', $customerNo);
        $query = $this->buildQuery ();
        $result = $this->client->ws_find_address (100, $query);
        $xml = $result ['GP_resultList'];
        $rowCount = $result ['rowCount'];
        $xmlObj = simplexml_load_string ($xml);
        return $xmlObj;
      }
    }
    // interessant für hallo-ping
    //$result = $this->client->ws_VMVersion ();
  }

?>



