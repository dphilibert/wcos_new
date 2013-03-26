<?php
  /**
   * Modul Übersicht
   *
   * @author Thomas Grahammer
   * @version $id$
   *
   */
  class Uebersicht_IndexController extends Zend_Controller_Action
  {
    /**
     * Zeigt die Account-Infos an
     *
     * @param void
     * @return void
     */
    public function indexAction ()
    { 
      $model = new Model_DbTable_AnbieterData ();
      $session = new Zend_Session_Namespace ();                       
      $provider = $model->getAnbieterDetails ($session->anbieterData ['anbieterID']);
      
      if (!empty ($provider ['STARTDATUM']))
      {  
        $parts = explode ('-', $provider ['STARTDATUM']); 
        $timestamp_end = mktime (0, 0, 0, $parts [1] + $provider ['LAUFZEIT'], $parts [2], $parts [0]);
      }
            
      $this->view->startdatum = (!empty ($provider ['STARTDATUM'])) ? date ('d.m.Y', mktime (0, 0, 0, $parts [1], $parts [2], $parts [0])) : '-';            
      $this->view->enddatum = (!empty ($provider ['STARTDATUM'])) ? date ('d.m.Y', $timestamp_end) : '-';    
      $this->view->restlaufzeit = (!empty ($provider ['STARTDATUM'])) ? round (($timestamp_end - mktime (0, 0, 0, date ('m'), date ('d'), date ('Y'))) / (60 * 60 * 24)) : '0';
      $this->view->name = $provider ['FIRMENNAME'];
      $this->view->premiumLevel = $provider ['PREMIUMLEVEL'];      
      $this->view->lastLogin = $provider ['LASTCHANGE'];
      $this->view->userstatus = $session->userData ['userStatus'];
    }
    
    public function statusAction ()
    {
      $this->_helper->_layout->disableLayout ();
      $this->_helper->viewRenderer->setNoRender (true);
      
      $model = new Model_DbTable_AnbieterData ();
      $anbieter = $model->getAnbieterDetails ($this->_request->getParam ('anbieterID'));                  
      $session = new Zend_Session_Namespace ();
            
      $status_new = ($anbieter ['PREMIUMLEVEL'] == 0) ? 1 : 0;  
      $session->anbieterData ['premiumLevel'] = $status_new;
      $model->saveAnbieter ('premiumLevel', $status_new, $this->_request->getParam ('anbieterID'));      
      if ($status_new == 1)
      {  
        $time_model = new Model_DbTable_LaufzeitData ();           
        $time_model->setLaufzeit ($this->_request->getParam ('anbieterID'), 12);
      }
      
      $this->_helper->json->sendJson (array ('status' => $status_new));                     
    }
  }

?>