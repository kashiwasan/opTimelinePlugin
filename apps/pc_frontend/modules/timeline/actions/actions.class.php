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
 * @author     Your name here
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
      $activityData = Doctrine::getTable('ActivityData')->findAll();
      $ac = array();
      foreach ($activityData as $activity)
      {
        $id = $activity->getId();
        $memberId = $activity->getMemberId(); 
        $body = $activity->getBody();
        $uri = $activity->getUri();
        $source = $activity->getSource();
        $sourceUri = $activity->getSourceUri();
        $createdAt = $activity->getCreateAt();
        $ac[] = array( 'id' => $id, 'memberId' => $memberId, 'body' => $body, 'uri' => $uri, 'source' => $source, 'sourceUri' => $sourceUri, 'createdAt' => $createdAt, ); 
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
        $activityData = Doctrine::getTable('ActivityData')->retrieveByInReplyToMemberId($memberSelfId);
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
    $token = $form->getCSRFToken($request->getParameter('secretKey'));
    if ($token=!$request->getParemeter('CSRFtoken'))
    {
      $json = array('status' => 'error', 'message' => 'Error. Invalid CSRF token Key.');
      return $this->renderText(json_encode($json));
    }
    
  }

  public function executeDelete(sfWebRequest $request)
  {

  }

  public function executegetCRSFToken(sfWebRequest $request)
  {
    $form = new sfForm();
    mt_srand();
    $secretkey = rand(10000, 999999);
    $token = $form->getCSRFToken($secretToken);
    return $this->renderText(json_encode(array( 'status' => 'success', 'token' => $token, 'secretKey' => $secretKey, )));
  }

}
