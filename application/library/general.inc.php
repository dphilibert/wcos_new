<?php

  /**
   * decoded einen utf8-String
   *
   * @param $item
   * @param $key
   */
  function utfDecode (&$item, $key)
  {
    $item = utf8_decode ($item);
  }

  /**
   * encoded einen string in utf8
   *
   * @param $item
   * @param $key
   */
  function utfEncode (&$item, $key)
  {
    $item = utf8_encode ($item);
  }

  /**
   * versendet eine Mail, dass sich Daten geändert haben
   *
   * @param $anbieterID Anbieter
   * @param string $wasGeaendert Beschreibung der Änderung
   * @param null $aenderung Änderung
   */
  function dataChangeMail ($anbieterID, $wasGeaendert = 'Daten', $aenderung = NULL)
  {
    $config = Zend_Registry::get ('config');
    $anbieterModel = new Model_DbTable_AnbieterData();
    $anbieterData = $anbieterModel->getAnbieter ($anbieterID);
    $stammdatenModel = new Model_DbTable_StammdatenData ();
    $stammdaten = $stammdatenModel->getStammdaten ($anbieterID);
    $stammdaten = $stammdaten [0];
    $premiumHash = md5 ($anbieterData ['anbieterhash']);
    $premiumLink = 'http://' . $_SERVER ['SERVER_NAME'] . '/stammdaten/index/makeitpremium/hash/' . $premiumHash;
    $mail = new Zend_Mail ();
    $mailHtml = 'Der Anbieter "' . $anbieterData ['firmenname'] . '" hat seine ' . $wasGeaendert . ' geändert.<br><br>';
    $mailHtml .= $aenderung;
    $mailHtml = utf8_decode ($mailHtml);
    //$mail->setBodyText ($mailText);
    $mail->setBodyHtml ($mailHtml);
    $mail->setFrom ($config->mail->from->address, $config->mail->from->text);
    $mail->addTo ($config->mail->to->address);
    $mail->setSubject ('WCOS Systemnachricht: Datenänderung');
    $mail->send ();    
  }

?>
