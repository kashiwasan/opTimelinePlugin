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
 * @author     Yoichi Kimura <yoichi.kimura@tejimaya.com>
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */
class timelineActions extends opApiActions
{
  public function executeList(sfWebRequest $request)
  {
    $this->activityData = Doctrine::getTable('ActivityData')->createQuery('ad')
      ->where('ad.in_reply_to_activity_id IS NULL')
      ->andWhere('ad.foreign_table IS NULL')
      ->andWhere('ad.foreign_id IS NULL')
      ->andWhere('ad.public_flag = ?', 1)
      ->orderBy('ad.id DESC')
      ->limit(20)
      ->execute();

    $this->baseUrl = sfConfig::get('op_base_url');
    $this->viewMemberId = $this->getMember()->getId();

    $this->getResponse()->setContentType('application/json');
  }

  public function executePost(sfWebRequest $request)
  {
    $this->getResponse()->setContentType('application/json');
    if ($token=!$request->getParameter('body'))
    {
      $this->status = 'error';
      $this->message = 'Error. Body is null.';
      return sfView::SUCCESS;
    }
    $activity = new ActivityData();
    $activity->setMemberId($this->getMember()->getId()); 
    $activity->setBody(htmlspecialchars($request->getParameter('body'), ENT_QUOTES));
    $mentions = opTimelinePluginUtil::hasScreenName($request->getParameter('body'));
    if (!is_null($mentions))
    {
      $activity->setTemplate('mention_member_id');
      $activity->setTemplateParam($mentions);
    }
    $inReplyToActivityId = $request->getParameter('replyId');
    if (isset($inReplyToActivityId) && is_numeric($inReplyToActivityId))
    {
      $activity->setInReplyToActivityId($inReplyToActivityId);
    }
    $foreign = $request->getParameter('foreign');
    $foreignId = $request->getParameter('foreignId');
    if (isset($foreign) && isset($foreignId) && is_numeric($foreignId))
    {
      $activity->setForeignTable($foreign); 
      $activity->setForeignId($foreignId);
    }
    $activity->setPublicFlag(1);
    $activity->save();
    $this->status = 'success';
    $this->message = "Update request was suceed!";
    return sfView::SUCCESS;
  }

  public function executeDelete(sfWebRequest $request)
  {
    $activityId = $request->getParameter('activityId');
    if (!isset($activityId) || !is_numeric($activityId))
    {
      $this->status = 'error';
      $this->message = 'Error. Activity Id is not set.';
      return sfVIew::SUCCESS;
    }
    $memberId = $this->getUser()->getMemberId();
    $activityData = Doctrine::getTable('ActivityData')->findByIdAndMemberId($activityId, $memberId);
    if (!$activityData)
    {
      $this->status = 'error';
      $this->message = 'Error . Your Request Activity Id is not exist.';
      return sfView::SUCCESS;
    }
    $activityData->delete();
    $this->status = 'success';
    $this->message = 'Your Delete Request has been succeed!';
    return sfView::SUCCESS;
  }
  
}
