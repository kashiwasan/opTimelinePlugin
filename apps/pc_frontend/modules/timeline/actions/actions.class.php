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
 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    if ($this->isSmt())
    {
      return $this->executeSmtIndex($request);
    }

    return sfView::SUCCESS;
  }

  public function executeSmtIndex(sfWebRequest $request)
  {
    $this->setLayout('smtLayoutHome');

    return sfView::SUCCESS;
  }

  public function executeMember(sfWebRequest $request)
  {
    if ($this->isSmt())
    {
      return $this->executeSmtMember($request);
    }
    $this->memberId = $request->getParameter('id', $this->getUser()->getMember()->getId());

    return sfView::SUCCESS;
  }

  public function executeCommunity(sfWebRequest $request)
  {
    if ($this->isSmt())
    {
      return $this->executeSmtCommunity($request);
    }
    $this->communityId = $request->getParameter('id');
    $this->community = Doctrine::getTable('Community')->find($this->communityId);
    $this->forward404Unless($this->community, 'Undefined community.');
    sfConfig::set('sf_nav_type', 'community');
    $this->baseUrl = sfConfig::get('op_base_url');
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
    return sfView::SUCCESS;
  }

  public function executeShow($request)
  {
    if ($this->isSmt())
    {
      return $this->executeSmtShow($request);
    }

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

  public function executeSmtShow($request)
  {
    $this->setLayout('smtLayoutSns');
    $this->setTemplate('smtShow');
    $activityId = (int)$request['id'];
    $this->activity = Doctrine::getTable('ActivityData')->find($activityId);
    if (!$this->activity)
    {
      return sfView::ERROR;
    }
    $this->comment = Doctrine_Query::create()->from('ActivityData ad')->where('ad.in_reply_to_activity_id = ?', $activityId)->execute();

    return sfView::SUCCESS; 
  }

  public function executeSmtMember($request)
  {
    $this->memberId = (int)$request->getParameter('id', $this->getUser()->getMember()->getId());
    $this->member = Doctrine::getTable('Member')->find($this->memberId);
    $this->setLayout('smtLayoutMember');
    $this->getResponse()->setDisplayMember($this->member);  
    $this->setTemplate('smtMember');

    return sfView::SUCCESS;
  }

  public function executeSmtCommunity($request)
  {
    $this->communityId = (int)$request->getParameter('id');
    $this->community = Doctrine::getTable('Community')->find($this->communityId);

    $this->setLayout('smtLayoutGroup');
    $this->getResponse()->setDisplayCommunity($this->community);  
    $this->setTemplate('smtCommunity');

    return sfView::SUCCESS;
  }

  public function executeMentions(sfWebRequest $request)
  {

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

  private function isSmt()
  {
    return (preg_match('/iPhone/', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Android/', $_SERVER['HTTP_USER_AGENT']));
  }
}
