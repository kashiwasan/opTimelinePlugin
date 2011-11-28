<?php

/**
 * opGyoenKintaiPlugin components.
 *
 * @package    OpenPNE
 * @subpackage opTimelinePlugin
 * @author     Shouta Kashiwagi
 */

class timelineComponents extends sfComponents
{
  public function executeTimelineAll(sfWebRequest $request)
  {
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/custom-theme/jquery-ui-1.8.16.custom.css');
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/prettyPopin.css');
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/gorgon-home.css');
    if (is_null(sfConfig::get('op_jquery_url', null)))
    {
      $this->getResponse()->addJavascript('https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.js');
    } 
    $this->getResponse()->addJavascript('http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.js');
    $this->getResponse()->addJavascript('/opTimelinePlugin/js/jquery.timeline.js');
    $this->getResponse()->addJavascript('/opTimelinePlugin/js/gorgon.js');
    $this->baseUrl = sfConfig::get('op_base_url');
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
    return sfView::SUCCESS;
  }

  public function executeTimelineGadget(sfWebRequest $request)
  {
    $this->getResponse()->addStylesheet('/opTimelinePlugin/css/custom-theme/jquery-ui-1.8.16.custom.css', 'first');
    $this->getResponse()->addStylesheet('/opTimelinePlugin/css/prettyPopin.css', 'first');
    $this->getResponse()->addStylesheet('/opTimelinePlugin/css/custom-theme/timeline.css', 'first');
    $this->baseUrl = sfConfig::get('op_base_url'); 
  }

  public function executeTimelineCommunity(sfWebRequest $request)
  {

    $this->getResponse()->addStylesheet('/opTimelinePlugin/css/custom-theme/jquery-ui-1.8.16.custom.css', 'first');
    $this->getResponse()->addStylesheet('/opTimelinePlugin/css/prettyPopin.css', 'first');
    $this->getResponse()->addStylesheet('/opTimelinePlugin/css/custom-theme/timeline.css', 'first');
    $this->baseUrl = sfConfig::get('op_base_url'); 
    //  $this->baseUrl = sfConfig::get('op_base_url');
    $this->cid = $request->getParameter('id');
    $this->foreigntable = "community";
    $this->mode = 2;    

  }
}

