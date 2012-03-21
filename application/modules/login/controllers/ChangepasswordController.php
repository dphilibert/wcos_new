<?php

  /**
   * Controller für Passwortänderung im Modul Login
   *
   * @depricated
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Login_ChangepasswordController extends Zend_Controller_Action
  {

    /**
     * leitet auf /login/index/index/error/changepassword um
     *
     * @return void
     */
    public function init ()
    {
      //$this->_helper->_layout->disableLayout ();
      // rendern abschalten, damit die Fancybox angezeigt werden kann ohne Fehler zu produzieren
      $this->_helper->_layout->disableLayout ();
      $this->_helper->viewRenderer->setNoRender (true);
      $this->_redirect ('/login/index/index/error/changepassword');
    }


    public function indexAction ()
    {
//die ('tod');
    }

    public function errorAction ()
    {
    }
  }

?>
