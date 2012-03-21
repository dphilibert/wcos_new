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

  /**
   * holt einen Adressdatensatz aus dem VM
   *
   * @param string $searchValue Suchbegriff
   * @param string $xml
   * @return mixed
   */
  public function getAdress ($searchValue = NULL, $xml = NULL)
  {
    $client = new SoapClient("http://217.111.48.221:18080/4DWSDL", array('login' => 'TGrahammer',
      'password' => 'rxyust56#',
    ));

    $options = $xml;

    $result = $client->ws_find_address (100, $options);

    $xml = $result ['GP_resultList'];


    // interessant für hallo-ping

    //$result = $client->ws_VMVersion ();

    $xmlObj = simplexml_load_string ($xml);

    $ret = $xmlObj->addressPool->addressPool->lastName;

    return $ret;
  }

}

?>



