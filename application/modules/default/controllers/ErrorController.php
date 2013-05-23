<?php

  /**
   * Fallback (default) Error-Handling
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class ErrorController extends Zend_Controller_Action
  {

    /**
     * initiales Init
     *
     * @return void
     */
    public function init ()
    {
      $this->_helper->viewRenderer->setNoRender ();
    }

    /**
     * leitet im Fehlerfall auf die Uebersicht-Seite um bzw. gibt Fehlermeldung aus
     *
     * @return void
     *
     */
    public function errorAction ()
    {
      $errors = $this->_getParam ('error_handler');
      switch ($errors->type)
      {
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
          $this->_helper->redirector->gotoUrl ('/einfuehrung/index/index');
          break;
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
          // 404 Fehler -- Controller oder Aktion nicht gefunden
          $this->getResponse ()
          ->setRawHeader ('HTTP/1.1 404 Not Found');
          // ... Ausgabe fü Anzeige erzeugen...
          $this->_helper->redirector->gotoUrl ('/einfuehrung/index/index');
          break;
        default:         
          $exception = $errors->exception;          
          $message = $exception->getMessage ();          
          echo $message;                    
          break;
      }
    }
  }

?>
