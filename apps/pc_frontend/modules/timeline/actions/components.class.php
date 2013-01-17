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
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/jquery.colorbox.css');
    $this->getResponse()->addJavascript('/opTimelinePlugin/js/jquery.colorbox.js', 'last');
    $this->getResponse()->addJavascript('/opTimelinePlugin/js/jquery.timeline.js', 'last');

    $this->publicFlags = Doctrine::getTable('ActivityData')->getPublicFlags();
    $this->viewPhoto = opTimeline::getViewPhoto();

    $this->_setFileMaxSize();

    return sfView::SUCCESS;
  }

  private function _setFileMaxSize()
  {
    $fileMaxSize = array();
    $fileMaxSize['format'] = opTimelinePluginUtil::getFileSizeMaxOfFormat();
    $fileMaxSize['size'] = opTimelinePluginUtil::getFileSizeMax();

    $this->fileMaxSize = $fileMaxSize;
  }

  public function executeTimelineProfile(sfWebRequest $request)
  {
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/jquery.colorbox.css');
    $this->getResponse()->addJavascript('/opTimelinePlugin/js/jquery.colorbox.js', 'last');
    $this->getResponse()->addJavascript('/opTimelinePlugin/js/jquery.timeline.js', 'last');
    if ($request->hasParameter('id'))
    {
      $this->memberId = $request->getParameter('id');
    }
    else
    {
      $this->memberId = $this->getUser()->getMember()->getId();
    }

    $this->viewPhoto = opTimeline::getViewPhoto();

    $this->_setFileMaxSize();

    return sfView::SUCCESS;
  }

  public function executeTimelineCommunity(sfWebRequest $request)
  {
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/jquery.colorbox.css');
    $this->getResponse()->addJavascript('/opTimelinePlugin/js/jquery.colorbox.js', 'last');
    $this->getResponse()->addJavascript('/opTimelinePlugin/js/jquery.timeline.js', 'last');

    $this->publicFlags = Doctrine::getTable('ActivityData')->getPublicFlags();
    $this->viewPhoto = opTimeline::getViewPhoto();

    $this->_setFileMaxSize();

    $this->memberId = $this->getUser()->getMember()->getId();
  }

  public function executeCommunityTimelineBy5(sfWebRequest $request)
  {
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/gorgon-home.css');
    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/blueandgreen.css');
    $this->communityId = $request->getParameter('id');
    $this->activityData =  Doctrine_Query::create()
      ->from('ActivityData ad')
      ->where('ad.in_reply_to_activity_id IS NULL')
      ->andWhere('ad.foreign_table = ?', 'community')
      ->andWhere('ad.foreign_id = ?', $this->communityId)
      ->andWhere('ad.public_flag = ?', 1)
      ->orderBy('ad.id DESC')
      ->limit(3)
      ->execute();

    $this->_setFileMaxSize();
  }

  public function executeSmtTimeline(sfWebRequest $request)
  {
    $this->baseUrl = sfConfig::get('op_base_url');
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
    $this->viewPhoto = opTimeline::getViewPhoto();

    $this->_setFileMaxSize();

    return sfView::SUCCESS;
  }

  public function executeSmtMemberTimelineBy1(sfWebRequest $request)
  {
    $this->memberId = $request->getParameter('id', $this->getUser()->getMemberId());
    $this->activityData =  Doctrine_Query::create()
      ->from('ActivityData ad')
      ->where('ad.in_reply_to_activity_id IS NULL')
      ->andWhere('ad.member_id = ?', $this->memberId)
      ->andWhere('ad.foreign_table IS NULL')
      ->andWhere('ad.foreign_id IS NULL')
      ->andWhere('ad.public_flag = ?', 1)
      ->orderBy('ad.id DESC')
      ->limit(1)
      ->execute();
    if ($this->activityData)
    {
      $this->createdAt = $this->activityData[0]->getCreatedAt();
      $this->body = $this->activityData[0]->getBody();
    }

    $this->_setFileMaxSize();
  }

  public function executeSmtCommunityTimelineBy1(sfWebRequest $request)
  {
    $this->communityId = $request->getParameter('id');
    $this->activityData =  Doctrine_Query::create()
       ->from('ActivityData ad')
       ->where('ad.in_reply_to_activity_id IS NULL')
       ->andWhere('ad.foreign_table = ?', 'community')
       ->andWhere('ad.foreign_id = ?', $this->communityId)
       ->andWhere('ad.public_flag = ?', 1)
       ->orderBy('ad.id DESC')
       ->limit(1)
       ->execute();
    if ($this->activityData)
    {
      $this->createdAt = $this->activityData[0]->getCreatedAt();
      $this->body = $this->activityData[0]->getBody();
    }
    $this->memberId = $this->getUser()->getMemberId();
    $this->community = Doctrine::getTable('Community')->find($this->communityId);

    $this->_setFileMaxSize();
  }

  public function executeSmtTimelineBy1(sfWebRequest $request)
  {
    $this->activityData =  Doctrine_Query::create()
       ->from('ActivityData ad')
       ->where('ad.in_reply_to_activity_id IS NULL')
       ->andWhere('ad.foreign_table IS NULL')
       ->andWhere('ad.public_flag = ?', 1)
       ->orderBy('ad.id DESC')
       ->limit(1)
       ->execute();
    if ($this->activityData)
    {
      $this->createdAt = $this->activityData[0]->getCreatedAt();
      $this->body = $this->activityData[0]->getBody();
    }

    $this->_setFileMaxSize();
  }

  public function executeSmtTimelineMember(sfWebRequest $request)
  {
    $this->id = $request->getParameter('id');
    $this->member = Doctrine::getTable('Member')->find($this->id);
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
    $this->viewPhoto = opTimeline::getViewPhoto();

    $this->_setFileMaxSize();
  }

  public function executeSmtTimelineCommunity(sfWebRequest $request)
  {
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
    $this->id = $request->getParameter('id');
    $this->viewPhoto = opTimeline::getViewPhoto();

    $this->_setFileMaxSize();
  }
}

