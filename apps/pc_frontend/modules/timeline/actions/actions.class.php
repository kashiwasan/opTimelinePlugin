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
    $this->baseUrl = sfConfig::get('op_base_url');
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
    return sfView::SUCCESS;
  }

  public function executeMentions(sfWebRequest $request)
  {
    $this->baseUrl = sfConfig::get('op_base_url');
    $form = new sfForm();
    $this->token = $form->getCSRFToken();
    return sfView::SUCCESS;
  }

  public function executeList(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Helper', 'Date', 'sfImage', 'opUtil',));
    $baseUrl = sfConfig::get('op_base_url');
    $ac = array();
    $activityIds = array();
    $activityData = Doctrine_Query::create()->from('ActivityData ad')->where('ad.in_reply_to_activity_id IS NULL')->andWhere('ad.foreign_table IS NULL')->andWhere('ad.foreign_id IS NULL')->andWhere('ad.public_flag = ?', 1)->orderBy('ad.id DESC')->limit(20)->execute();
    foreach ($activityData as $activity)
    {
      $id = $activity->getId();
      $memberId = $activity->getMemberId();
      $member = Doctrine::getTable('Member')->find($memberId);
      if (!$member->getImageFileName())
      {
        $memberImage = $baseUrl . '/images/no_image.gif';
      }
      else
      {
        $memberImageFile = $member->getImageFileName();
        $memberImage = sf_image_path($memberImageFile, array('size' => '48x48',));
      }
      $memberName = $member->getName();
      $memberScreenName = $this->getScreenName($memberId) ? $this->getScreenName($memberId) : $memberName;
      $body = opTimelinePluginUtil::screenNameReplace($activity->getBody(), $baseUrl);
      $uri = $activity->getUri();
      $source = $activity->getSource();
      $sourceUri = $activity->getSourceUri();
      $createdAt = $activity->getCreatedAt();
  
      if ($memberId==$this->getUser()->getMember()->getId())
      {
        $deleteLink = 'show';
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
    $commentData = Doctrine_Query::create()->from('ActivityData ad')->where('ad.in_reply_to_activity_id IS NOT NULL')->andWhere('ad.foreign_table IS NULL')->andWhere('ad.foreign_id IS NULL')->andWhere('ad.public_flag = ?', 1)->execute();
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
          $cm['memberScreenName'] = $this->getScreenName($cm['memberId']) ? $this->getScreenName($cm['memberId']) : $cm['memberName'];
          $cm['body'] = opTimelinePluginUtil::screenNameReplace($activity->getBody(), $baseUrl);
          if ($cm['memberId']==$this->getUser()->getMember()->getId())
          {
            $cm['deleteLink'] = 'show';
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
          $activityIds[] = $cm['id'];
        }
      }
      $i++;
    }

    $tls = Doctrine_Query::create()->from('TimelineLike tl')->whereIn('tl.activity_id', $activityIds)->orderBy('tl.id DESC')->execute();
    foreach ($tls as $tl)
    {
      $activityDataId = $tl->getActivityDataId();
      if ($ac[$activityDataId])
      {
        $ac[$activityDataId]['like'] = array(
          'id' => $tl->getId(),
          'memberId' => $tl->getMemberId(),
          'memberScreenName' => $this->getScreenName($tl->getMemberId()),
          'creeatedAt' => $tl->getCreatedAt(),
        );
      }

      for ($i=0;$i<$count;$i++)
      {
        if($ac[$i]['reply'][$activityDataId])
        {
          $ac[$i]['reply'][$activityDataId]['like'] = array(
            'id' => $tl->getId(),
            'memberId' =>  $tl->getMemberId(),
            'memberScreenName' => $this->getScreenName($tl->getMemberId()),
            'createdAt' => $tl->getCreatedAt(),
          );
        }
      }
    }

    $json = array( 'status' => 'success', 'data' => $ac, );
    return $this->renderText(json_encode($json, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_QUOT));
  }

  public function executeListCommunity(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Helper', 'Date', 'sfImage', 'opUtil',));
    $baseUrl = sfConfig::get('op_base_url');
    $cid = $request->getParameter('id');
    if (!is_numeric($cid))
    {
      $json = array('status' => 'error', 'message' => 'your request id is not numeric.',);
      return $this->renderText(json_encode($json));
    }
    $ac = array();
    $activityData = Doctrine_Query::create()->from('ActivityData ad')->where('ad.in_reply_to_activity_id IS NULL')->andWhere('ad.public_flag = ?', 1)->andWhere('ad.foreign_table = ?', 'community')->andWhere('ad.foreign_id = ?', $cid)->orderBy('ad.id DESC')->limit(20)->execute();
    foreach ($activityData as $activity)
    {
      $id = $activity->getId();
      $memberId = $activity->getMemberId();
      $member = Doctrine::getTable('Member')->find($memberId);
      if (!$member->getImageFileName())
      {
        $memberImage = $baseUrl . '/images/no_image.gif';
      }
      else
      {
        $memberImageFile = $member->getImageFileName();
        $memberImage = sf_image_path($memberImageFile, array('size' => '48x48',));
      }
      $memberName = $member->getName();
      $memberScreenName = $this->getScreenName($memberId) ? $this->getScreenName($memberId) : $memberName;
      $body = opTimelinePluginUtil::screenNameReplace($activity->getBody(), $baseUrl);
      $uri = $activity->getUri();
      $source = $activity->getSource();
      $sourceUri = $activity->getSourceUri();
      $createdAt = $activity->getCreatedAt();

      if ($memberId==$this->getUser()->getMember()->getId())
      {
        $deleteLink = 'show';
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
    }
    $count = count($ac); 
    $i = 0;
    $commentData = Doctrine_Query::create()->from('ActivityData ad')->where('ad.in_reply_to_activity_id IS NOT NULL')->andWhere('ad.foreign_table = ?', 'community')->andWhere('ad.foreign_id = ?', $cid)->andWhere('ad.public_flag = ?', 1)->execute();
    foreach ($commentData as $activity)
    {
      for ($j=0;$j<$count;$j++)
      {
        if ($ac[$j]['id']==$inReplyToActivityId)
        {
          $member = Doctrine::getTable('Member')->find($activity->getMemberId());
          $cm = array();
          $cm['id'] = $activity->getId();
          $cm['memberId'] = $member->getId();
          $cm['memberName'] = $member->getName();
          $cm['memberScreenName'] = $this->getScreenName($cm['memberId']) ? $this->getScreenName($cm['memberId']) : $cm['memberName'];
          $cm['body'] = opTimelinePluginUtil::screenNameReplace($activity->getBody(), $baseUrl);
          if ($cm['memberId']==$this->getUser()->getMember()->getId())
          {
            $cm['deleteLink'] = 'show';
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
    }

    $i++;

    $json = array( 'status' => 'success', 'data' => $ac, );
    return $this->renderText(json_encode($json, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_QUOT));
  }

  public function executeListMention(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Helper', 'Date', 'sfImage', 'opUtil',));
    $baseUrl = sfConfig::get('op_base_url');
    $memberId = $this->getUser()->getMember()->getId();
    $mines = Doctrine::getTable('ActivityData')->findByMemberId($memberId);
    $replyId = array();
    foreach ($mines as $mine)
    {
      $replyId[] = $mine->getId();
    }
 
    $activityData = Doctrine_Query::create()->from('ActivityData ad')->where('ad.template = ?', 'mention_member_id')->andWhere('ad.template_param LIKE ?', '%|' . $memberId . '|%')->execute();
    foreach($activityData as $activity)
    {
      $id = $activity->getId();
      $memberId = $activity->getMemberId();
      $member = Doctrine::getTable('Member')->find($memberId);
      if (!$member->getImageFileName())
      {
        $memberImage = $baseUrl . '/images/no_image.gif';
      }
      else
      {
        $memberImageFile = $member->getImageFileName();
        $memberImage = sf_image_path($memberImageFile, array('size' => '48x48',));
      }
      $memberName = $member->getName();
      $memberScreenName = $this->getScreenName($memberId) ? $this->getScreenName($memberId) : $memberName;
      $body = opTimelinePluginUtil::screenNameReplace($activity->getBody(), $baseUrl);
      $uri = $activity->getUri();
      $source = $activity->getSource();
      $sourceUri = $activity->getSourceUri();
      $createdAt = $activity->getCreatedAt();

      if ($memberId==$this->getUser()->getMember()->getId())
      {
        $deleteLink = 'show';
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
    }
    $json = array( 'status' => 'success', 'data' => $ac, );
    return $this->renderText(json_encode($json, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_QUOT));
  }

  public function executePost(sfWebRequest $request)
  {
    $form = new sfForm(); 
    $token = $form->getCSRFToken();
    if ($token=!$request->getParameter('CSRFtoken'))
    {
      $json = array('status' => 'error', 'message' => 'Error. Invalid CSRF token Key.');
      return $this->renderText(json_encode($json));
    }
    if ($token=!$request->getParameter('body'))
    {
      $json = array('status' => 'error', 'message' => 'Error. Body is null.',);
      return $this->renderText(json_encode($json));
    }
    $activity = new ActivityData();
    $activity->setMemberId($this->getUser()->getMemberId()); 
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
    $json = array( 'status' => 'success', 'message' => 'UPDATE was succeed!', );
    return $this->renderText(json_encode($json));
  }

  public function executeDelete(sfWebRequest $request)
  {
    $form = new sfForm();
    $token = $form->getCSRFToken();
    if ($token=!$request->getParameter('CSRFtoken'))
    {
      $json = array('status' => 'error', 'message' => 'Error. Invalid CSRF token key.', );
      return $this->renderText(json_encode($json));
    }
    $activityId = $request->getParameter('activityId');
    if (!isset($activityId) || !is_numeric($activityId))
    {
      $json = array( 'status' => 'error', 'message' => 'Error. Activity Id is not set.');
      return $this->renderText(json_encode($json));
    }
    $memberId = $this->getUser()->getMemberId();

    $activityData = Doctrine::getTable('ActivityData')->findByIdAndMemberId($activityId, $memberId);

    if (!$activityData)
    {
      $json = array( 'status' => 'error', 'message' => 'Error. Your Request Activity Id does not exist.',);
      return $this->renderText(json_encode($json));
    }
    $activityData->delete();
    $json = array( 'status' => 'success', 'message' => 'Your Delete Request has been succeed!' );
    return $this->renderText(json_encode($json));
  }

  public function executeGetCSRFToken(sfWebRequest $request)
  {
    $form = new sfForm();
    $token = $form->getCSRFToken($secretKey);
    return $this->renderText(json_encode(array( 'status' => 'success', 'token' => $token, )));
  }

  public function executeJs(sfWebRequest $request)
  {
    $this->baseUrl = sfConfig::get('op_base_url');
    $this->mode = $request->getParameter('mode');
    $this->cid = $request->getParameter('cid');
    if ($this->mode==2)
    {
      $this->foreigntable = 'community';
    }
    $form = new sfForm();
    $this->csrfToken = $form->getCSRFToken();
    return sfView::SUCCESS;
  }

/************
  public function executeListDifference(sfWebRequest $request)
  {
    $lastId = $request->getRequest('lastId');
    $m = $request->getParameter('m');
    if (is_null($m))
    {
      if (!is_null($lasiId) && is_numeric($lastId))
      {
        Doctrine_Query::create()->from('ActivityData ad')->where('ad.in_reply_to_activity_id IS NULL')->andWhere('ad.public_flag = ?', 1)->andWhere('ad.foreign_table = ?', 'community')->andWhere('ad.foreign_id' = ?)->orderBy('ad.id DESC')->limit(20)->execute();
      }
    }
    elseif($m="community")
    {
      if (!is_null($lastid) && is_numeric($lastId))
      {
        Doctrine_Query::create()->from('ActivityData ad')->where('ad.in_reply_to_activity_id IS NULL')->andWhere('ad.public_flag = ?' 1)->andWhere('ad.foreign_table IS NULL')->execute();
      }
    }
  }
***********/

  public function executeTimelinePlugin(sfWebRequest $request)
  {
    $this->baseUrl = sfConfig::get('op_base_url');
    $form = new sfForm();
    $this->csrfToken = $form->getCSRFToken();
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
