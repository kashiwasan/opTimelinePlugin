<?php

/**
 * opGyoenKintaiPlugin components.
 *
 * @package    OpenPNE
 * @subpackage opTimelinePlugin
 * @author     Shouta Kashiwagi <kashiwagi@tejimaya.com>
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
    $this->getResponse()->addJavascript('/opTimelinePlugin/js/gorgon-gadget.js');
    $this->baseUrl = sfConfig::get('op_base_url');
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
    return sfView::SUCCESS;
  }

  public function executeTimelineProfile(sfWebRequest $request)
  {
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/custom-theme/jquery-ui-1.8.16.custom.css');
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/prettyPopin.css');
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/gorgon-home.css');
    $this->getResponse()->addJavascript('https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.js');
    $this->getResponse()->addJavascript('http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.js');
    $this->getResponse()->addJavascript('/opTimelinePlugin/js/jquery.timeline.js');
    // $this->getResponse()->addJavascript('/opTimelinePlugin/js/gorgon.js');
    $this->baseUrl = sfConfig::get('op_base_url');
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
    $this->memberId = $request->getParameter('id');
    return sfView::SUCCESS;
  }

  public function executeTimelineCommunity(sfWebRequest $request)
  {
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/custom-theme/jquery-ui-1.8.16.custom.css');
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/prettyPopin.css');
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/gorgon-home.css');
    $this->getResponse()->addJavascript('https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.js');
    $this->getResponse()->addJavascript('http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.js');
    $this->getResponse()->addJavascript('/opTimelinePlugin/js/jquery.timeline.js');
    // $this->getResponse()->addJavascript('/opTimelinePlugin/js/gorgon.js');
    $this->baseUrl = sfConfig::get('op_base_url');
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
    $this->cid = $request->getParameter('id');
    $this->foreigntable = "community";
    $this->mode = 2;
  }

  public function executeSmtTimeline(sfWebRequest $request)
  {
    $this->baseUrl = sfConfig::get('op_base_url');
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
    return sfView::SUCCESS;
  }

  public function executeSmtMemberTimelineBy1(sfWebRequest $request)
  {
    $this->memberId = $request->getParameter('id');
    $this->activityData =  Doctrine_Query::create()->from('ActivityData ad')->where('ad.in_reply_to_activity_id IS NULL')->andWhere('ad.member_id = ?', $this->memberId)->andWhere('ad.foreign_table IS NULL')->andWhere('ad.foreign_id IS NULL')->andWhere('ad.public_flag = ?', 1)->orderBy('ad.id DESC')->limit(1)->execute();
    if ($this->activityData)
    {
      $this->createdAt = $this->activityData[0]->getCreatedAt();
      $this->body = sfOutputEscaper::escape(sfConfig::get('sf_escaping_method'), opTimelinePluginUtil::screenNameReplace($this->activityData[0]->getBody(), sfConfig::get('op_base_url')));
    }
  }

  public function executeSmtTimelineMember(sfWebRequest $request)
  {
    $this->id = $request->getParameter('id');
    $this->member = Doctrine::getTable('Member')->find($this->id);
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
  }

  public function executeSmtTimelineCommunity(sfWebRequest $request)
  {
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
    $this->id = $request->getParameter('id');
  }
}

