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

  }

  public function executeList(sfWebRequest $request)
  {
    $mode = $request->getParameter('mode'); 
    if (!$mode)
    {
      $mode = 1;      
    }
    if ($mode=1)
    {
      $ac = array();
      $activityData = Doctrine::getTable('ActivityData')->findAll();
      foreach ($activityData as $activity)
      {
        $inReplyToActivityId = $activity->getInReplyToActivityId();
        if (!isset($inReplyToActivityId))
        { 
          $id = $activity->getId();
          $memberId = $activity->getMemberId();
          $member = Doctrine::getTable('Member')->find($memberId);
          $memberImage = $member->getImageFileName();
          // $memberScreenName = $member->getProfile('op_screen_name', true);
          $memberScreenName = $member->getName();
          $body = $activity->getBody();
          $uri = $activity->getUri();
          $source = $activity->getSource();
          $sourceUri = $activity->getSourceUri();
          $createdAt = $activity->getCreatedAt();
          $ac[] = array( 'id' => $id, 'memberId' => $memberId, 'memberImage' => $memberImage, 'memberScreenName' => $memberScreenName, 'body' => $body, 'uri' => $uri, 'source' => $source, 'sourceUri' => $sourceUri, 'createdAt' => $createdAt, ); 
        }
      }
      $count = count($ac); 
      $i = 0;
      foreach ($activityData as $activity)
      {
        $inReplyToActivityId = $activity->getInReplyToActivityId();
        if (isset($inReplyToActivityId))
        {
          for($j=0;$j<$count;$j++)
          {
            if($ac[$j]['id']==$inReplyToActivityId){
              $member = Doctrine::getTable('Member')->find($activity->getMemberId());
              $cm = array();
              $cm['id'] = $activity->getId();
              $cm['memberId'] = $member->getId();
              //$cm['memberScreenName'] = $member->getProfile('op_screen_name', true);
              $cm['memberScreenName'] = $member->getName();
              $cm['body'] = $activity->getBody();
              $cm['uri'] = $activity->getUri();
              $cm['source'] = $activity->getSource();
              $cm['sourceUri'] = $activity->getSourceUri();
              $cm['createdAt'] = $activity->getCreatedAt();
              $ac[$j]['reply'][] = $cm;
            }
          }
        }
        $i++;
      }

      $json = array( 'status' => 'success', 'data' => $ac, );
      return $this->renderText(json_encode($json));
    }
    elseif ($mode=2)
    {
      $memberSelfId = $request->getParameter('memberId');
      if (is_null($memberSelfId))
      {
        $json = array('status' => 'error');
        return $this->renderText(json_encode($json));
      }
      else
      {
        $ac = array();
        $activityData = Doctrine::getTable('ActivityData')->retrieveByInReplyToActivityId($memberSelfId);
        foreach ($activityData as $activity)
        {
          $id = $activity->getId();
          $memberId = $activity->getMemberId();
          $body = $activity->getBody();
          $uri = $activity->getUri();
          $source = $activity->getSource();
          $sourceUri = $activity->getSource();
          $createdAt = $activity->getCreatedAt();
          $ac[] = array('id' => $id, 'memberId' => $memberId, 'body' => $body, 'uri' => $uri, 'source' => $source, 'sourceUri' => $sourceUri, 'createdAt' => $createdAt, ); 
        }
        $json = array('status' => 'success', 'data' => $ac);
        return $this->renderText();
      }    
    }
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
    $activity = new ActivityData();
    $activity->setMemberId($this->getUser()->getMemberId()); 
    $activity->setBody($request->getParameter('body'));
    $inReplyToActivityId = $request->getParameter('replyId');
    if (isset($inReplyToActivityId))
    {
      $activity->setInReplyToMemberId($inReplyToActivityId);
    }
    $foreign = $request->getParameter('forign');
    $foreignId = $request->getParameter('foreignId');
    if (isset($foreign) && isset($foreignId))
    {
      $activity->setForeign($foreign); 
      $activity->setForeignId($foreignId);
    }
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
    if (!isset($activityId))
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
}
