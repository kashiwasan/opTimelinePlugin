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
  public function executeKintaiGadget(sfWebRequest $request)
  {
    $this->getResponse()->addJavascript('/opGyoenKintaiPlugin/js/jquery-1.6.4.min.js', 'first');
    $this->getResponse()->addJavascript('/opGyoenKintaiPlugin/js/jquery.prettyPopin.js', 'first');
    $this->getResponse()->addJavascript('/opGyoenKintaiPlugin/js/jquery.kintai.js');
    $this->member = $this->getUser()->getMember();
    $this->mode = $request->getParameter('mode');
    $this->json = $request->getParameter('json');
    
  }
}

