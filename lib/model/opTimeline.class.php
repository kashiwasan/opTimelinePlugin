<?php

class opTimeline
{

  public function addPublicFlagForActivityDatas($activityDatas)
  {
    $ids = array();
    foreach ($activityDatas as $data)
    {
      $ids[] = $data['id'];
    }

    $publicStatusList = $this->getPublicStatusListByIds($ids);

    $returnDatas = array();

    foreach ($activityDatas as $data)
    {
      $data['public_status'] = $publicStatusList[$data['id']];
      $returnDatas[] = $data;
    }

    return $returnDatas;
  }

  public function getPublicStatusListByIds($ids)
  {
    $query = new opDoctrineQuery();

    $query->select('id, public_flag');
    $query->from('ActivityData');
    $query->andWhereIn('id', $ids);

    $fetchData = $query->fetchArray();

    $publicStatusTextList = array(
        ActivityDataTable::PUBLIC_FLAG_OPEN => 'open',
        ActivityDataTable::PUBLIC_FLAG_SNS => 'sns',
        ActivityDataTable::PUBLIC_FLAG_FRIEND => 'friend',
        ActivityDataTable::PUBLIC_FLAG_PRIVATE => 'private'
    );

    $publicFlags = array();
    foreach ($fetchData as $data)
    {
      $publicStatues[$data['id']] = $publicStatusTextList[(int) $data['public_flag']];
    }

    return $publicStatues;
  }

  public function addImageUrlForContent(array $apiDatas)
  {
    $ids = array();
    foreach ($apiDatas as $data)
    {
      $ids[] = $data['id'];
    }

    if (empty($ids))
    {
      return $apiDatas;
    }

    $query = new opDoctrineQuery();
    $query->select('activity_data_id, uri');
    $query->from('ActivityImage');
    $query->andWhereIn('activity_data_id', $ids);

    $searchResult = $query->fetchArray();

    $imageUrls = array();
    foreach ($searchResult as $row) {
      $imageUrls[$row['activity_data_id']] = $row['uri'];
    }

    foreach ($apiDatas as &$data)
    {
      $id = $data['id'];
      
      if (isset($imageUrls[$id])) {
        $data['body'] = $data['body'].' '.$imageUrls[$id];
        $data['body_html'] = $data['body_html'].'<div><img src="'.$imageUrls[$id].'"></div>';
      }

    }

    return $apiDatas;
  }

  public function createPostActivityFromAPIByApiDataAndMemberId($apiData, $memberId)
  {
    $body = (string) $apiData['body'];

    $options = array();

    if (isset($apiData['public_flag']))
    {
      $options['public_flag'] = $apiData['public_flag'];
    }

    if (isset($apiData['in_reply_to_activity_id']))
    {
      $options['in_reply_to_activity_id'] = $apiData['in_reply_to_activity_id'];
    }

    if (isset($apiData['uri']))
    {
      $options['uri'] = $apiData['uri'];
    }
    elseif (isset($apiData['url']))
    {
      $options['uri'] = $apiData['url'];
    }

    if (isset($apiData['target']) && 'community' === $apiData['target'])
    {
      $options['foreign_table'] = 'community';
      $options['foreign_id'] = $apiData['target_id'];
    }

    $options['source'] = 'API';

    return Doctrine::getTable('ActivityData')->updateActivity($memberId, $body, $options);
  }

    /**
   *
   * TODO
   * ファイル画像をOpenPNE方式に変更する
   *
   * @todo ファイル画像の保存方式をOpenPNE方式に変更する
   * @todo ファイル画像の容量をリサイズする
   */
  public function createActivityImagesaveByFileInfoAndActivityId(array $fileInfo, $activityId)
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
    $activityImage->setActivityDataId($activityId);
    $activityImage->setFileId($file->getId());
    $activityImage->setUri($fileInfo['web_base_path'].$uploadBasePath.'/'.$filename);
    $activityImage->setMimeType($file->type);
    $activityImage->save();

    return $activityImage;
  }


  public function getViewPhoto()
  {
    $viewPhoto = Doctrine::getTable('SnsConfig')->get('op_timeline_plugin_view_photo', false);
    if (false !== $viewPhoto)
    {
      return $viewPhoto;
    }
    return 1;
  }
}
