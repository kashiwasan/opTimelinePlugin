<?php

class opTimeline
{

  public function addPublicFlagForActivityDatas($activityDatas)
  {
    $ids = array();
    foreach ($activityDatas['data'] as $data)
    {
      $ids[] = $data->id;
    }

    $publicStatusList = $this->getPublicStatusListByIds($ids);

    $returnDatas = array();
    $returnDatas['status'] = $activityDatas['status'];

    foreach ($activityDatas['data'] as $data)
    {
      $data->public_status = $publicStatusList[$data->id];
      $returnDatas['data'][] = $data;
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
    foreach ($apiDatas['data'] as $data)
    {
      $ids[] = $data->id;
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

    foreach ($apiDatas['data'] as &$data)
    {
      $id = $data->id;
      
      if (isset($imageUrls[$id])) {
        $data->body = $data->body.' '.$imageUrls[$id];
        $data->body_html = $data->body_html.' '.$imageUrls[$id];
      }

    }

    return $apiDatas;
  }

}
