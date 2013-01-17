<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * timeline actions.
 *
 * @package    OpenPNE
 * @subpackage timeline
 * @author     Shouta Kashiwagi <kashiwagi@tejimaya.com>
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */

class timelineActions extends sfActions
{
  public function executeMember(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartphone(), 'timeline', 'smtMember');

    $this->memberId = $request->getParameter('id', $this->getUser()->getMember()->getId());

    return sfView::SUCCESS;
  }

  public function executeCommunity(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartphone(), 'timeline', 'smtCommunity');

    $this->communityId = $request->getParameter('id');
    $this->community = Doctrine::getTable('Community')->find($this->communityId);
    $this->forward404Unless($this->community, 'Undefined community.');
    sfConfig::set('sf_nav_type', 'community');
    $this->baseUrl = sfConfig::get('op_base_url');
    $form = new sfForm();
    $this->token = $form->getCSRFToken();

    return sfView::SUCCESS;
  }

  public function executeShow(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartphone(), 'timeline', 'smtShow');

    $this->getResponse()->addStyleSheet('/opTimelinePlugin/css/jquery.colorbox.css');
    $this->getResponse()->addJavascript('/opTimelinePlugin/js/jquery.colorbox.js', 'last');

    $activityId = (int)$request['id'];
    $this->activity = Doctrine::getTable('ActivityData')->find($activityId);
    if (!$this->activity)
    {
      return sfView::ERROR;
    }
    $this->comment = Doctrine_Query::create()->from('ActivityData ad')->where('ad.in_reply_to_activity_id = ?', $activityId)->execute();

    return sfView::SUCCESS; 
  }

  public function executeSns(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartphone(), 'timeline', 'smtSns');
    $this->forward('default', 'error');

    return sfView::SUCCESS; 
  }

  public function executeSmtSns(opWebRequest $request)
  {
    $this->baseUrl = sfConfig::get('op_base_url');
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
    $this->viewPhoto = opTimeline::getViewPhoto();

    $this->setTemplate('smtSns');

    return sfView::SUCCESS; 
  }

  public function executeSmtShow(opWebRequest $request)
  {
    $activityId = (int)$request['id'];
    $this->activity = Doctrine::getTable('ActivityData')->find($activityId);
    if (!$this->activity)
    {
      return sfView::ERROR;
    }
    $this->comment = Doctrine_Query::create()->from('ActivityData ad')->where('ad.in_reply_to_activity_id = ?', $activityId)->execute();

    return sfView::SUCCESS; 
  }

  public function executeSmtMember(opWebRequest $request)
  {
    $this->memberId = (int)$request->getParameter('id', $this->getUser()->getMember()->getId());
    $this->member = Doctrine::getTable('Member')->find($this->memberId);
    opSmartphoneLayoutUtil::setLayoutParameters(array('member' => $this->member));
    $this->setTemplate('smtMember');

    return sfView::SUCCESS;
  }

  public function executeSmtCommunity(opWebRequest $request)
  {
    $this->communityId = (int)$request->getParameter('id');
    $this->community = Doctrine::getTable('Community')->find($this->communityId);
    $this->forward404If(!$this->community->isPrivilegeBelong($this->getUser()->getMemberId()));
    opSmartphoneLayoutUtil::setLayoutParameters(array('community' => $this->community));
    $this->setTemplate('smtCommunity');

    return sfView::SUCCESS;
  }

  public function executeTimelinePlugin(sfWebRequest $request)
  {
    return sfView::SUCCESS;
  }

  private function getScreenName($memberId)
  {
    $memberConfig = Doctrine::getTable('MemberConfig')->findOneByMemberIdAndName($memberId, 'op_screen_name');
    if ($memberConfig)
    {
      return "@".$memberConfig->getValue();
    }
    else
    {
      return false;
    }
  }
}
