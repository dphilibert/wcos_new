<?php

  /**
   * default Index
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class IndexController extends Zend_Controller_Action
  {

    /**
     * leitet auf die Login-Seite um
     *
     * @return void
     *
     */
    public function indexAction ()
    {
      $this->_helper->redirector->gotoUrl ('/login/index/index');
      //logDebug ("default index Action", "tgr");
    }

  }

?>
