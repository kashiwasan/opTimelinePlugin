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
  public function executeTimelineGadget(sfWebRequest $request)
  {
    $this->getResponse()->addStylesheet('/opTimelinePlugin/css/custom-theme/jquery-ui-1.8.16.custom.css', 'first');
    $this->getResponse()->addStylesheet('/opTimelinePlugin/css/prettyPopin.css', 'first');
    $this->getResponse()->addStylesheet('/opTimelinePlugin/css/custom-theme/timeline.css', 'first');
    $this->baseUrl = sfConfig::get('op_base_url'); 
  }
}

