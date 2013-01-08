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
