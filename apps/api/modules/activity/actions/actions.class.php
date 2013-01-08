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

  public function executeCommentSearch(sfWebRequest $request)
  {
    $this->forward400If(!isset($request['timeline_id']) || '' === (string) $request['timeline_id'], 'timeline id is not specified');
    $limit = isset($request['count']) ? $request['count'] : sfConfig::get('op_json_api_limit', 15);

    $timelineId = $request['timeline_id'];
    $activity = Doctrine::getTable('ActivityData')->find($timelineId);

    if (0 < count($activity))
    {
      $this->replies = $activity->getReplies(ActivityDataTable::PUBLIC_FLAG_SNS, $limit);
    }
  }

  public function executePost(sfWebRequest $request)
  {
    $errorInfo = $this->_checkParameterReturnArray($request);

    if (!empty($errorInfo))
    {
      return $this->_renderJSONDirect($errorInfo);
    }

    if (!empty($_FILES) && (int) $_FILES['timeline-submit-upload']['size'] !== 0)
    {
      $fileInfo = $this->_createFileInfo($request);

      $errorFileInfo = $this->_checkImageUploadByFileInfoReturnArray($fileInfo);

      if (!empty($errorFileInfo))
      {
        return $this->_renderJSONDirect($errorFileInfo);
      }
    }

    $this->_operateTweet($request);

    $this->_loadHelperForUseopJsonAPI();
    $acEntity = op_api_activity($this->activity);

    $replies = $this->activity->getReplies();
    if (0 !== count($replies))
    {
      $acEntity['replies'] = array();

      foreach ($replies as $reply)
      {
        $acEntity['replies'][] = op_api_activity($reply);
      }
    }

    if (!empty($_FILES) && (int) $_FILES['timeline-submit-upload']['size'] !== 0)
    {
      $fileUploadInfo = $this->_saveFileByFileInfo($fileInfo);
      return $this->_renderJSONDirect(array('status' => 'success', 'message' => 'file up success', 'data' => $acEntity));
    }

    return $this->_renderJSONDirect(array('status' => 'success', 'message' => 'tweet success', 'data' => $acEntity));
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

  private function _createFileInfo(sfWebRequest $request)
  {
    //開発を簡単にするためにコメントアウト
    $fileInfo = $_FILES['timeline-submit-upload'];
    $fileInfo['stream'] = fopen($fileInfo['tmp_name'], 'r');
    $fileInfo['dir_name'] = '/a'.$this->getUser()->getMember()->getId();
    $fileInfo['binary'] = stream_get_contents($fileInfo['stream']);
    $fileInfo['web_base_path'] = $request->getUriPrefix().$request->getRelativeUrlRoot();

    return $fileInfo;
  }

  //activityのpostアクションをコピーしている
  private function _operateTweet(sfWebRequest $request)
  {
    $body = (string) $request['body'];

    $memberId = $this->getUser()->getMemberId();
    $options = array();

    if (isset($request['public_flag']))
    {
      $options['public_flag'] = $request['public_flag'];
    }

    if (isset($request['in_reply_to_activity_id']))
    {
      $options['in_reply_to_activity_id'] = $request['in_reply_to_activity_id'];
    }

    if (isset($request['uri']))
    {
      $options['uri'] = $request['uri'];
    }
    elseif (isset($request['url']))
    {
      $options['uri'] = $request['url'];
    }

    if (isset($request['target']) && 'community' === $request['target'])
    {
      $options['foreign_table'] = 'community';
      $options['foreign_id'] = $request['target_id'];
    }

    $options['source'] = 'API';

    $this->activity = Doctrine::getTable('ActivityData')->updateActivity($memberId, $body, $options);

    $this->activityId = $this->activity->getId();

    if ('1' === $request['forceHtml'])
    {
      // workaround for some browsers (see #3201)
      $this->getRequest()->setRequestFormat('html');
      $this->getResponse()->setContentType('text/html');
    }

    $this->setTemplate('object');
  }

  private function _checkParameterReturnArray(sfWebRequest $request)
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

  private function _checkImageUploadByFileInfoReturnArray(array $fileInfo)
  {
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

    return array();
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

  /**
   *
   * TODO
   * ファイル画像をOpenPNE方式に変更する
   *
   * @todo ファイル画像の保存方式をOpenPNE方式に変更する
   * @todo ファイル画像の容量をリサイズする
   */
  private function _saveFileByFileInfo(array $fileInfo)
  {

    $file = new File();
    $file->setOriginalFilename(basename($fileInfo['name']));
    $file->setType($fileInfo['type']);

    $filename = md5(time()).'.'.$file->getImageFormat();

    $file->setName($fileInfo['dir_name'].'/'.$filename);
    $file->setFilesize($fileInfo['size']);

    $bin = new FileBin();
    $bin->setBin($fileInfo['binary']);
    $file->setFileBin($bin);

    $file->save();

    //@todo OpenPNEの保存形式に変更する

    $uploadBasePath = '/cache/img/'.$file->getImageFormat();

    $uploadDirPath = sfConfig::get('sf_web_dir').$uploadBasePath;

    if (!file_exists($uploadDirPath))
    {
      mkdir($uploadDirPath, 0777, true);
    }

    $fileSavePath = $uploadDirPath.'/'.$filename;

    copy($fileInfo['tmp_name'], $fileSavePath);

    $activityImage = new ActivityImage();
    $activityImage->setActivityDataId($this->activityId);
    $activityImage->setFileId($file->getId());
    $activityImage->setUri($fileInfo['web_base_path'].$uploadBasePath.'/'.$filename);
    $activityImage->setMimeType($file->type);
    $activityImage->save();

    return true;
  }

  public function executeSearch(sfWebRequest $request)
  {

    $parameters = $request->getGetParameters();

    if (isset($parameters['target']))
    {
      $this->forward400IfInvalidTarget($parameters);
    }

    $activityDatas = $this->_activitySearchAPI($request);
    $timeline = new opTimeline();

    $activityDatas = $timeline->addPublicFlagForActivityDatas($activityDatas);
    $activityDatas = $timeline->addImageUrlForContent($activityDatas);

    return $this->renderJSON(array('status' => 'success', 'data' => $activityDatas));
  }

  /**
   *
   */
  private function _activitySearchAPI(sfWebRequest $request)
  {
    $builder = opActivityQueryBuilder::create()
                    ->setViewerId($this->getUser()->getMemberId());

    if (isset($request['target']))
    {
      if ('friend' === $request['target'])
      {
        $builder->includeFriends($request['target_id'] ? $request['target_id'] : null);
      }
      elseif ('community' === $request['target'])
      {
        $this->forward400Unless($request['target_id'], 'target_id parameter not specified.');
        $builder
                ->includeSelf()
                ->includeFriends()
                ->includeSns()
                ->setCommunityId($request['target_id']);
      }
      else
      {
        $this->forward400('target parameter is invalid.');
      }
    }
    else
    {
      if (isset($request['member_id']))
      {
        $builder->includeMember($request['member_id']);
      }
      else
      {
        $builder
                ->includeSns()
                ->includeFriends()
                ->includeSelf();
      }
    }

    $query = $builder->buildQuery();

    if (isset($request['keyword']))
    {
      $query->andWhereLike('body', $request['keyword']);
    }

    $globalAPILimit = sfConfig::get('op_json_api_limit', 20);
    if (isset($request['count']) && (int) $request['count'] < $globalAPILimit)
    {
      $query->limit($request['count']);
    }
    else
    {
      $query->limit($globalAPILimit);
    }

    if (isset($request['max_id']))
    {
      $query->addWhere('id <= ?', $request['max_id']);
    }

    if (isset($request['since_id']))
    {
      $query->addWhere('id > ?', $request['since_id']);
    }

    if (isset($request['activity_id']))
    {
      $query->addWhere('id = ?', $request['activity_id']);
    }

    $activityData = $query
                    ->andWhere('in_reply_to_activity_id IS NULL')
                    ->execute();

    $ac = array();

    $this->_loadHelperForUseopJsonAPI();
    foreach ($activityData as $activity)
    {
      $acEntity = op_api_activity($activity);

      $replies = $activity->getReplies();
      if (0 !== count($replies))
      {
        $acEntity['replies'] = array();

        $acEntity['repliesCount'] = $activity->getRepliesCount();
        foreach ($replies as $reply)
        {
          $acEntity['replies'][] = op_api_activity($reply);
        }
      }

      $ac[] = $acEntity;
    }

    return $ac;
  }

  private function _loadHelperForUseopJsonAPI()
  {
    //op_api_activityを使用するために必要なヘルパーを読み込む
    $this->getContext()->getConfiguration()->loadHelpers('opJsonApi');
    $this->getContext()->getConfiguration()->loadHelpers('opUtil');
    $this->getContext()->getConfiguration()->loadHelpers('Asset');
    $this->getContext()->getConfiguration()->loadHelpers('Helper');
    $this->getContext()->getConfiguration()->loadHelpers('Tag');
    $this->getContext()->getConfiguration()->loadHelpers('sfImage');
  }

  private function forward400IfInvalidTarget(array $params)
  {
    $validTargets = array('friend', 'community');

    if (!in_array($params['target'], $validTargets))
    {
      return $this->forward400('target parameter is invalid.');
    }

    if ($params['target'] === 'community')
    {
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
