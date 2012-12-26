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

  public function executeImageUpload(sfWebRequest $request)
  {
    //開発を簡単にするためにコメントアウト
    $fileInfo = $_FILES['timeline-submit-upload'];

    $stream = fopen($fileInfo['tmp_name'], 'r');

    if ($stream === false)
    {
      return $this->renderJSON(array('status' => 'error', 'message' => 'file upload error'));
    }

    if (!$this->_isImageUploadByFileInfo($fileInfo))
    {
      return $this->renderJSON(array('status' => 'error', 'message' => 'not image'));
    }

    $fileInfo['dir_name'] = '/a'.$this->getUser()->getMember()->getId();
    $fileInfo['binary'] = stream_get_contents($stream);
    $fileInfo['actvity_id'] = $_POST['id'];

    $fileInfo['web_base_path'] = $request->getUriPrefix().$request->getRelativeUrlRoot();

    $fileUploadInfo = $this->_saveFileByFileInfo($fileInfo);

    return $this->renderJSON(array('status' => 'success', 'message' => 'file up success'));
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
    $activityImage->setActivityDataId($fileInfo['actvity_id']);
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
