<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */
class timelineActions extends opJsonApiActions
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
      return $this->renderJSON($errorInfo);
    }

    if ((int) $_FILES['timeline-submit-upload']['size'] !== 0)
    {
      $fileInfo = $this->_createFileInfo($request);

      $errorFileInfo = $this->_checkImageUploadByFileInfoReturnArray($fileInfo);

      if (!empty($errorFileInfo))
      {
        return $this->renderJSON($errorFileInfo);
      }
    }

    $this->_operateTweet($request);

    if ((int) $_FILES['timeline-submit-upload']['size'] !== 0)
    {
      $fileUploadInfo = $this->_saveFileByFileInfo($fileInfo);

      return $this->renderJSON(array('status' => 'success', 'message' => 'file up success'));
    }

    return $this->renderJSON(array('status' => 'success', 'message' => 'tweet success'));
  }

  public function renderJSON(array $datas)
  {
    header("Content-Type: application/json; charset=utf-8");
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
    $fileInfo['actvity_id'] = $_POST['id'];

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
    $file = new File();
    $file->setType($fileInfo['type']);

    return $file->isImage();
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

    //実行の仕方自体はアクティビティの検索と同じなので、アクティビティ検索APIを使用する
    //@todo 本体のアクティビティ検索の部分をmodel化して同じクラスを使用するようにする
    $apiDatas = (array) json_decode($this->fetchApiData('activity/search'));

    $timeline = new opTimeline();

    $returnDatas = $timeline->addPublicFlagForActivityDatas($apiDatas);
    $returnDatas = $timeline->addImageUrlForContent($apiDatas);

    return $this->renderJson($returnDatas);
  }

  private function fetchApiData($apiName)
  {
    $moduleName = sfContext::getInstance()->getModuleName();
    $actionName = sfContext::getInstance()->getActionName();
    $currentApiName = $moduleName.'/'.$actionName;

    $currentUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $callApiUrl = str_replace($currentApiName, $apiName, $currentUrl);

    return file_get_contents($callApiUrl);
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

}
