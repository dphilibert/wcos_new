<?php

/**
 * globale Infos
 *
 * @author Thomas Grahammer
 * @version $id$
 *
 */

  class Plugin_GlobalInfo extends Zend_Controller_Plugin_Abstract
  {

    /**
     * Userdaten aus der Session
     * @var object Userdaten
     */
    var $userData = NULL;


   /**
    * holt die Sessiondaten
    *
    * @param Zend_Controller_Request_Abstract $request
    *
    * @return void
    */
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
      $config = Zend_Registry::get ('config');
      $sessionNamespace = new Zend_Session_Namespace ();
      $this->userData = $sessionNamespace->userData;
      if (is_array ($this->userData) && array_key_exists ('hash', $this->userData))
      {
        $sessionUserHash = $this->userData ['hash'];
        $layout = Zend_Layout::getMvcInstance ();
        $this->setAnbieterData (&$layout);
      }
    }

/**
 * setzt die Anbieterdaten im Layout
 *
 * @param $layout
 *
 * @return void
 */
    public function setAnbieterData ($layout)
    {
      $anbieterID = $this->userData ['anbieterID'];
      if ($anbieterID > 0)
      {
        $anbieterModel = new Model_DbTable_AnbieterData ();
        $anbieterDetails = $anbieterModel->getAnbieterDetails ($anbieterID);
        $layout->anbieterName = $anbieterDetails ['FIRMENNAME'];
      }
    }

  }

?>
