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

  public function executeGet(sfWebRequest $request)
  {
    $this->getResponse()->setContentType('application/json');
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Helper', 'Date', 'I18N', 'sfImage', 'Url', 'Tag', 'opUtil','Escaping', 'opTimeline'));
    $ac = array();
    $activityIds = array();
    $mode = $request->getParameter('mode');
    $list = $request->getParameter('list');
    $memberId = (int) $request->getParameter('memberId');
    $lastId = (int) $request->getParameter('lastId'); 
    $moreId = (int) $request->getParameter('moreId');
    $limit = (int) $request->getParameter('limit', 20);
    $communityId = (int) $request->getParameter('communityId');
    $activityData = Doctrine_Query::create()->from('ActivityData ad');

    switch ($list)
    {
      case 'all':
        $activityData = $activityData->where('ad.in_reply_to_activity_id IS NULL');
        break;

      case 'more':
        if (!is_numeric($moreId))
        {
          $this->status = 'error';
          $this->message = 'Request parameter "moreId" must be numeric.';

          return sfView::ERROR;
        }
        $activityData = $activityData->where('ad.id < ?', $moreId)
                                     ->andWhere('ad.in_reply_to_activity_id IS NULL');
        break;

      case 'check':
        if (!is_numeric($lastId))
        {
          $this->status = 'error';
          $this->message = 'Request parameter "lastId" must be numeric.';

          return sfView::ERROR;
        }
        $activityData = $activityData->where('ad.in_reply_to_activity_id IS NULL')
                                     ->andWhere('ad.id > ?', $lastId);
        break;
      default:
        $activityData = $activityData->where('ad.in_reply_to_activity_id IS NULL');
    }

    switch ($mode)
    {
      case 'member':
        if (!is_numeric($memberId))
        {
          $this->status = 'error';
          $this->message = 'Request parameter "memberId" must be numeric.';

          return sfView::ERROR;
        }
        $activityData = $activityData->andWhere('ad.member_id = ?', $memberId)
                                     ->andWhere('ad.foreign_table IS NULL')
                                     ->andWhere('ad.foreign_id IS NULL')
                                     ->andWhere('ad.public_flag = ?', 1)
                                     ->orderBy('ad.id DESC');
        break;
      case 'community':
        if (!is_numeric($communityId))
        {
          $this->status = 'error';
          $this->message = 'Request parameter "communityId" must be numeric.';

          return sfView::ERROR;
        }
        $activityData = $activityData->andWhere('ad.foreign_table = ?', 'community')
                                     ->andWhere('ad.foreign_id = ?', $communityId)
                                     ->orderBy('ad.id DESC');
        break;
      default:
        $activityData = $activityData->andWhere('ad.foreign_table IS NULL')
                                     ->andWhere('ad.foreign_id IS NULL')
                                     ->andWhere('ad.public_flag = ?', 1);
        $activityData = $activityData->orderBy('ad.id DESC');
    }
    $activityData = $activityData->limit($limit);
    $activityData = $activityData->execute();

    foreach ($activityData as $activity)
    {
      $id = $activity->getId();
      $memberId = $activity->getMemberId();
      $member = Doctrine::getTable('Member')->find($memberId);
      if (!$member->getImageFileName())
      {
        $memberImage = url_for('@homepage') . '/images/no_image.gif';
      }
      else
      {
        $memberImageFile = $member->getImageFileName();
        $memberImage = sf_image_path($memberImageFile, array('size' => '48x48',));
      }
      $memberName = $member->getName();
      $memberScreenName = $this->getScreenName($memberId) ? $this->getScreenName($memberId) : $memberName;
      $body = sfOutputEscaper::escape(sfConfig::get('sf_escaping_method'), opTimelinePluginUtil::screenNameReplace($activity->getBody(), url_for('@homepage')));
      $body = op_timeline_plugin_body_filter($activity, $body);
      $uri = $activity->getUri();
      $source = $activity->getSource();
      $sourceUri = $activity->getSourceUri();
      $createdAt = $activity->getCreatedAt();
  
      if ($memberId==$this->getMember()->getId())
      {
        $deleteLink = 'inline';
      }
      else
      {
        $deleteLink = 'none';
      }
      $ac[] = array( 
        'id' => $id, 
        'memberId' => $memberId, 
        'memberImage' => $memberImage, 
        'memberScreenName' => $memberScreenName, 
        'memberName' => $memberName,
        'body' => $body,  
        'deleteLink' => $deleteLink,
        'uri' => $uri, 
        'source' => $source, 
        'sourceUri' => $sourceUri, 
        'createdAt' => op_format_activity_time(strtotime($createdAt)), 
        'baseUrl' => sfConfig::get('op_base_url'),
      ); 
      $activityIds[] = $id;
    }

    $count = count($ac);
    $i = 0;

    $commentData = Doctrine_Query::create()->from('ActivityData ad')->whereIn('ad.in_reply_to_activity_id', $activityIds)->andWhere('ad.foreign_table IS NULL')->andWhere('ad.foreign_id IS NULL')->andWhere('ad.public_flag = ?', 1)->execute();
    foreach ($commentData as $activity)
    {
      $inReplyToActivityId = $activity->getInReplyToActivityId();
      for ($j=0;$j<$count;$j++)
      {
        if ($ac[$j]['id']==$inReplyToActivityId)
        {
          $member = Doctrine::getTable('Member')->find($activity->getMemberId());
          $cm = array();
          $cm['id'] = $activity->getId();
          $cm['memberId'] = $member->getId();
          $cm['memberName'] = $member->getName();
          if (!$member->getImageFileName())
          {
            $cm['memberImage'] = $baseUrl . '/images/no_image.gif';
          }
          else
          {
            $memberImageFile = $member->getImageFileName();
            $cm['memberImage'] = sf_image_path($memberImageFile, array('size' => '48x48',));
          }
          $cm['memberScreenName'] = $this->getScreenName($cm['memberId']) ? $this->getScreenName($cm['memberId']) : $cm['memberName'];
          $cm['body'] = opTimelinePluginUtil::screenNameReplace(sfOutputEscaper::escape(sfConfig::get('sf_escaping_method'), $activity->getBody()), $baseUrl);
          if ($cm['memberId']==$this->getMember()->getId())
          {
            $cm['deleteLink'] = 'inline';
          }
          else
          {
            $cm['deleteLink'] = 'none';
          }
          $cm['uri'] = $activity->getUri();
          $cm['source'] = $activity->getSource();
          $cm['sourceUri'] = $activity->getSourceUri();
          $cm['createdAt'] = op_format_activity_time(strtotime($activity->getCreatedAt()));
          $cm['baseUrl'] = sfConfig::get('op_base_url');
          $ac[$j]['reply'][] = $cm;
        }
      }
      $i++;
    }
    $this->status = 'success';
    $this->data = $ac;

    return sfView::SUCCESS;
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
    $memberId = $this->getMember()->getId();
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
