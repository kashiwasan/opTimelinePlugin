<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */
class activityActions extends opJsonApiActions
{

  const TWEET_MAX_LENGTH = 140;
  const COMMENT_DEFAULT_LIMIT = 15;

  /**
   * POSTAPIで作成されたアクティブデータモデル
   * 
   */
  private $_createdActivity;
  /**
   * @var opTimeline
   */
  private $_timeline;

  const DEFAULT_IMAGE_SIZE = 'large';

  public function preExecute()
  {
    parent::preExecute();

    $user = new opTimelineUser();
    
    $params = array();
    $params['image_size'] = $this->getRequestParameter('image_size', self::DEFAULT_IMAGE_SIZE);

    $request = sfContext::getInstance()->getRequest();
    $params['base_url'] = $request->getUriPrefix().$request->getRelativeUrlRoot();

    $this->_timeline = new opTimeline($user, $params);

    $this->_loadHelperForUseOpJsonAPI();
  }

  public function executeCommentSearch(sfWebRequest $request)
  {
    if (!isset($request['timeline_id']))
    {
      $this->forward400('timeline id is not specified');
    }

    if ('' === (string) $request['timeline_id'])
    {
      $this->forward400('timeline id is not specified');
    }

    $limit = isset($request['count']) ? $request['count'] : sfConfig::get('op_json_api_limit', self::COMMENT_DEFAULT_LIMIT);

    $timelineId = $request['timeline_id'];
    $activity = Doctrine::getTable('ActivityData')->find($timelineId);

    if (0 < count($activity))
    {
      $this->replies = $activity->getReplies(ActivityDataTable::PUBLIC_FLAG_SNS, $limit);
    }
  }

  public function executePost(sfWebRequest $request)
  {
    $errorResponse = $this->_getErrorResponseIfBadRequestOfPost($request);

    if (!is_null($errorResponse))
    {
      return $this->_renderJSONDirect($errorResponse);
    }

    $this->_createActivityDataByRequest($request);

    $responseData = $this->_createResponActivityDataOfPost();
    $responseData['body'] = htmlspecialchars($responseData['body'], ENT_QUOTES, 'UTF-8', false);
    $responseData['body_html'] = htmlspecialchars($responseData['body_html'], ENT_QUOTES, 'UTF-8', false);

    if ($this->_isUploadImagePost())
    {
      return $this->_renderJSONDirect(array('status' => 'success', 'message' => 'file up success', 'data' => $responseData));
    }

    return $this->_renderJSONDirect(array('status' => 'success', 'message' => 'tweet success', 'data' => $responseData));
  }

  private function _isUploadImagePost()
  {
    return (!empty($_FILES) && (int) $_FILES['timeline-submit-upload']['size'] !== 0);
  }

  private function _getErrorResponseIfBadRequestOfPost(sfWebRequest $request)
  {
    $errorInfo = $this->_getErrorResponseIfBadRequestOfTweetPost($request);

    if (!empty($errorInfo))
    {
      return $errorInfo;
    }

    if ($this->_isUploadImagePost())
    {
      $fileInfo = $this->_createFileInfo($request);

      if ($fileInfo['size'] >= opTimelinePluginUtil::getFileSizeMax())
      {
        return array('status' => 'error', 'message' => 'file size over', 'type' => 'file_size');
      }

      $stream = fopen($fileInfo['tmp_name'], 'r');

      if ($stream === false)
      {
        return array('status' => 'error', 'message' => 'file upload error', 'type' => 'upload');
      }

      if (!$this->_isImageUploadByFileInfo($fileInfo))
      {
        return array('status' => 'error', 'message' => 'not image', 'type' => 'not_image');
      }
    }

    return null;
  }

  private function _createResponActivityDataOfPost()
  {
    $this->_loadHelperForUseOpJsonAPI();
    $activity = op_api_activity($this->_createdActivity);

    $replies = $this->_createdActivity->getReplies();
    if (0 !== count($replies))
    {
      $activity['replies'] = array();

      foreach ($replies as $reply)
      {
        $activity['replies'][] = op_api_activity($reply);
      }
    }

    return $activity;
  }

  /**
   * なぜかPOSTAPIだとJSONレンダーがうまくうごかなかった
   */
  private function _renderJSONDirect(array $datas)
  {
    //header("Content-Type: application/json; charset=utf-8");
    echo json_encode($datas);
    exit;
  }

  /**
   * @todo ファイル情報じゃないのが含まれているので、それを分ける
   */
  private function _createFileInfo()
  {
    $request = sfContext::getInstance()->getRequest();

    //開発を簡単にするためにコメントアウト
    $fileInfo = $_FILES['timeline-submit-upload'];
    $fileInfo['stream'] = fopen($fileInfo['tmp_name'], 'r');
    $fileInfo['dir_name'] = '/a'.$this->getUser()->getMember()->getId();
    $fileInfo['binary'] = stream_get_contents($fileInfo['stream']);
    $fileInfo['web_base_path'] = $request->getUriPrefix().$request->getRelativeUrlRoot();
    $fileInfo['member_id'] = $this->getUser()->getMemberId();

    return $fileInfo;
  }

  private function _createActivityDataByRequest(sfWebRequest $request)
  {
    if ($request->isMethod('get'))
    {
      $saveData = $request->getGetParameters();
    }
    else
    {
      $saveData = $request->getPostParameters();
    }

    $memberId = $this->getUser()->getMemberId();

    $this->_createdActivity = $this->_timeline->createPostActivityFromAPIByApiDataAndMemberId($saveData, $memberId);

    if ($this->_isUploadImagePost())
    {
      $fileInfo = $this->_createFileInfo($request);
      $this->_timeline->createActivityImageByFileInfoAndActivityId($fileInfo, $this->_createdActivity->getId());
    }
  }

  private function _getErrorResponseIfBadRequestOfTweetPost(sfWebRequest $request)
  {
    $body = (string) $request['body'];

    $errorInfo = array('status' => 'error', 'type' => 'tweet');

    if (empty($body))
    {
      $errorInfo['message'] = 'body parameter not specified.';
      return $errorInfo;
    }

    if (mb_strlen($body) > self::TWEET_MAX_LENGTH)
    {
      $errorInfo['message'] = 'The body text is too long.';
      return $errorInfo;
    }

    if (isset($request['target']) && 'community' === $request['target'])
    {
      if (!isset($request['target_id']))
      {
        $errorInfo['message'] = 'target_id parameter not specified.';
        return $errorInfo;
      }
    }

    return null;
  }

  private function _isImageUploadByFileInfo(array $fileInfo)
  {
    foreach (opTimelinePluginUtil::getUploadAllowImageTypeList() as $type)
    {
      $contentType = 'image/'.$type;

      if ($fileInfo['type'] === $contentType)
      {
        return true;
      }
    }

    return false;
  }

  public function executeSearch(sfWebRequest $request)
  {
    $parameters = $request->getGetParameters();

    if (isset($parameters['target']))
    {
      $this->_forward400IfInvalidTargetForSearchAPI($parameters);
    }

    $activityDatas = $this->_timeline->searchActivityDatasByAPIRequestDatasAndMemberId(
                    $request->getGetParameters(), $this->getUser()->getMemberId());

    $activitySearchDatas = $activityDatas->getData();
    //一回も投稿していない
    if (empty($activitySearchDatas))
    {
      return $this->renderJSON(array('status' => 'success', 'data' => array()));
    }

    $responseDatas = $this->_timeline->createActivityDatasByActivityDataAndViewerMemberIdForSearchAPI(
                    $activityDatas, $this->getUser()->getMemberId());

    $responseDatas = $this->_timeline->addPublicFlagByActivityDatasForSearchAPIByActivityDatas($responseDatas, $activityDatas);
    $responseDatas = $this->_timeline->embedImageUrlToContentForSearchAPI($responseDatas);

    return $this->renderJSON(array('status' => 'success', 'data' => $responseDatas));
  }

  private function _activitySearchAPI(sfWebRequest $request)
  {
    
    return $responseDatas;
  }

  private function _loadHelperForUseOpJsonAPI()
  {
    //op_api_activityを使用するために必要なヘルパーを読み込む
    $this->getContext()->getConfiguration()->loadHelpers('opJsonApi');
    $this->getContext()->getConfiguration()->loadHelpers('opUtil');
    $this->getContext()->getConfiguration()->loadHelpers('Asset');
    $this->getContext()->getConfiguration()->loadHelpers('Helper');
    $this->getContext()->getConfiguration()->loadHelpers('Tag');
    $this->getContext()->getConfiguration()->loadHelpers('sfImage');
  }

  private function _forward400IfInvalidTargetForSearchAPI(array $params)
  {
    $validTargets = array('friend', 'community');

    if (!in_array($params['target'], $validTargets))
    {
      return $this->forward400('target parameter is invalid.');
    }

    if ($params['target'] === 'community')
    {
      $this->forward400Unless(
              Doctrine::getTable('CommunityMember')->isMember($this->getUser()->getMemberId(), $params['target_id']),
              'You are not community member'
              );

      $this->forward400Unless($params['target_id'], 'target_id parameter not specified.');
    }
  }

  public function executeMember(sfWebRequest $request)
  {
    if ($request['id'])
    {
      $request['member_id'] = $request['id'];
    }

    if (isset($request['target']))
    {
      unset($request['target']);
    }

    $this->forward('activity', 'search');
  }

  public function executeFriends(sfWebRequest $request)
  {
    $request['target'] = 'friend';

    if (isset($request['member_id']))
    {
      $request['target_id'] = $request['member_id'];
      unset($request['member_id']);
    }
    elseif (isset($request['id']))
    {
      $request['target_id'] = $request['id'];
      unset($request['id']);
    }

    $this->forward('activity', 'search');
  }

  public function executeCommunity(sfWebRequest $request)
  {
    $request['target'] = 'community';

    if (isset($request['community_id']))
    {
      $request['target_id'] = $request['community_id'];
      unset($request['community_id']);
    }
    elseif (isset($request['id']))
    {
      $request['target_id'] = $request['id'];
      unset($request['id']);
    }
    else
    {
      $this->forward400('community_id parameter not specified.');
    }

    $this->forward('activity', 'search');
  }

  public function executeDelete(sfWebRequest $request)
  {
    if (isset($request['activity_id']))
    {
      $activityId = $request['activity_id'];
    }
    elseif (isset($request['id']))
    {
      $activityId = $request['id'];
    }
    else
    {
      $this->forward400('activity_id parameter not specified.');
    }

    $activity = Doctrine::getTable('ActivityData')->find($activityId);

    $this->forward404Unless($activity, 'Invalid activity id.');

    $this->forward403Unless($activity->getMemberId() === $this->getUser()->getMemberId());

    $activity->delete();

    return $this->renderJSON(array('status' => 'success'));
  }

  public function executeMentions(sfWebRequest $request)
  {
    $builder = opActivityQueryBuilder::create()
                    ->setViewerId($this->getUser()->getMemberId())
                    ->includeMentions();

    $query = $builder->buildQuery()
                    ->andWhere('in_reply_to_activity_id IS NULL')
                    ->andWhere('foreign_table IS NULL')
                    ->andWhere('foreign_id IS NULL')
                    ->limit(20);

    $this->activityData = $query->execute();

    $this->setTemplate('array');
  }

}
