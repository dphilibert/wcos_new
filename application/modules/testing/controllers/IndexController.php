<?php

  /**
   * testing Index
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Testing_IndexController extends Zend_Controller_Action
  {

    public function init ()
    {
      $this->_helper->_layout->disableLayout ();
    }

    /**
     * leitet auf die Login-Seite um
     *
     * @return void
     *
     */
    public function indexAction ()
    {
      $extAPI = new Model_ExtAPI_Verlagsmanager();
      $dataSet = $extAPI->searchAddressByKundennummer ('1057850');
      logDebug (print_r ($dataSet, true), "tgr");
    }
  }

?>
