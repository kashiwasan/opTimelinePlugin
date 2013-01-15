<?php

class opTimeline
{
  const COMMENT_DISPLAY_MAX = 10;

  public function addPublicFlagByActivityDatasForSearchAPIByActivityDatas(array $responseDatas, $activityDatas)
  {
    $publicFlags = array();
    foreach ($activityDatas as $activity)
    {
      $publicFlags[$activity->getId()] = $activity->getPublicFlag();
    }

    $publicStatusTextList = array(
        ActivityDataTable::PUBLIC_FLAG_OPEN => 'open',
        ActivityDataTable::PUBLIC_FLAG_SNS => 'sns',
        ActivityDataTable::PUBLIC_FLAG_FRIEND => 'friend',
        ActivityDataTable::PUBLIC_FLAG_PRIVATE => 'private'
    );

    foreach ($responseDatas as &$data)
    {
      $publicFlag = $publicFlags[$data['id']];
      $data['public_status'] = $publicStatusTextList[$publicFlag];
    }
    unset($data);

    return $responseDatas;
  }

  /**
   * メソッドを実行する前にopJsonApiをロードしておく必要がある
   *
   * @todo 整形というには大仕事をしすぎているので名前をcreateにする
   */
  public function createActivityDatasByActivityDataAndViewerMemberIdForSearchAPI($activityDatas, $viewerMemberId)
  {
    $activityIds = array();

    foreach ($activityDatas as $activity)
    {
      $activityIds[] = $activity->getId();
    }

    $replayActivityDatas = $this->findReplayActivityDatasByActivityIdsGroupByActivityId($activityIds);

    $memberIds = $this->_extractionMemberIdByActivitieyDatasAndReplayActivityDataRows(
                    $activityDatas, $replayActivityDatas);
    $memberDatas = $this->createMemberDatasByViewerMemberIdAndMemberIdsForAPIResponse($viewerMemberId, $memberIds);

    $responseDatas = $this->_createActivityDatasByActivityDatasAndMemberDatasForSearchAPI($activityDatas, $memberDatas);

    foreach ($responseDatas as &$response)
    {
      $id = $response['id'];

      if (isset($replayActivityDatas[$id]))
      {
        $replaies = $replayActivityDatas[$id];
        $response['replies'] = $this->_createActivityDatasByActivityDataRowsAndMemberDatasForSearchAPI($replaies, $memberDatas);
      }
      else
      {
        $response['replies'] = null;
      }

    }
    unset($response);

    return $responseDatas;
  }

  private function _extractionMemberIdByActivitieyDatasAndReplayActivityDataRows($activities, $replayActivitiyRows)
  {
    $memberIds = array();
    foreach ($activities as $activity)
    {
      $memberIds[] = $activity->getMemberId();
    }

    foreach ($replayActivitiyRows as $ActivityDatas)
    {
      foreach ($ActivityDatas as $activityData)
      {
        $memberIds[] = $activityData['member_id'];
      }
    }

    $memberIds = array_unique($memberIds);

    return $memberIds;
  }

  private function _createActivityDatasByActivityDatasAndMemberDatasForSearchAPI($activityDatas, $memberDatas)
  {
    foreach ($activityDatas as $activity)
    {
      $activityIds[] = $activity->getId();
    }

    $activityImageUrls = $this->findActivityImageUrlsByActivityIds($activityIds);

    $responseDatas = array();
    foreach ($activityDatas as $activity)
    {
      if (isset($activityImageUrls[$activity->getId()]))
      {
        //@todo symfonyの形式に変更させる
        //$activityImageUrl = sf_image_path($activityImageUrls[$activity->getId()], array(), true);

        $activityImageUrl = $activityImageUrls[$activity->getId()];
      }
      else
      {
        $activityImageUrl = null;
      }

      $responseData['id'] = $activity->getId();
      $responseData['member'] = $memberDatas[$activity->getMemberId()];

      $responseData['body'] = $activity->getBody();
      $responseData['body_html'] = op_activity_linkification(nl2br(op_api_force_escape($activity->getBody())));
      $responseData['uri'] = $activity->getUri();
      $responseData['source'] = $activity->getSource();
      $responseData['source_uri'] = $activity->getSourceUri();

      //@todo イメージサイズを縮小したのを取得できるようにする
      $responseData['image_url'] = $activityImageUrl;
      $responseData['image_large_url'] = $activityImageUrl;
      $responseData['created_at'] = date('r', strtotime($activity->getCreatedAt()));

      $responseDatas[] = $responseData;
    }

    return $responseDatas;
  }

  private function _createActivityDatasByActivityDataRowsAndMemberDatasForSearchAPI($activityDataRows, $memberDatas)
  {

    $responseDatas = array();
    foreach ($activityDataRows as $row)
    {
      $responseData['id'] = $row['id'];
      $responseData['member'] = $memberDatas[$row['member_id']];

      $responseData['body'] = $row['body'];
      $responseData['body_html'] = op_activity_linkification(nl2br(op_api_force_escape($row['body'])));
      $responseData['uri'] = $row['uri'];
      $responseData['source'] = $row['source'];
      $responseData['source_uri'] = $row['source_uri'];

      //コメントでは画像を投稿できない
      $responseData['image_url'] = null;
      $responseData['image_large_url'] = null;
      $responseData['created_at'] = date('r', strtotime($row['created_at']));

      $responseDatas[] = $responseData;
    }

    return $responseDatas;
  }


  public function createMemberDatasByViewerMemberIdAndMemberIdsForAPIResponse($viewerMemberId, $memberIds)
  {

    $freindAndBlocks = $this->findFriendMemberIdsAndBlockMemberIdsByMemberId($viewerMemberId);
    $imageUrls = $this->findImageFileUrlsByMemberIds($memberIds);

    $introductionId = $this->findIntroductionIdFromProfile();
    $introductions = $this->findMemberIntroductionByMemberIdsAndIntroductionId($memberIds, $introductionId);

    $firendCounts = $this->findFriendCountByMemberIds($memberIds);

    $memberNames = $this->findMemberNamesByMemberIds($memberIds);

    $memberDatas = array();

    foreach ($memberIds as $memberId)
    {
      $memberData = array();
      $memberData['id'] = $memberId;
      $memberData['profile_image'] = $imageUrls[$memberId];
      $memberData['screen_name'] = $memberNames[$memberId];
      $memberData['name'] = $memberNames[$memberId];
      $memberData['profile_url'] = op_api_member_profile_url($memberId);
      $memberData['friend'] = isset($freindAndBlocks['friend'][$memberId]);
      $memberData['blocking'] = isset($freindAndBlocks['block'][$memberId]);
      $memberData['self'] = $viewerMemberId === $memberId;
      $memberData['friends_count'] = $firendCounts[$memberId];
      $memberData['self_introduction'] = isset($introductions[$memberId]) ? (string) $introductions[$memberId] : null;

      $memberDatas[$memberId] = $memberData;
    }

    return $memberDatas;
  }

  public function findReplayActivityDatasByActivityIdsGroupByActivityId(array $activityIds)
  {
    static $queryCacheHash;

    $q = Doctrine_Query::create();

    if (!$queryCacheHash)
    {
      $q = Doctrine_Query::create();
      $q->from('ActivityData ad');
      $q->whereIn('in_reply_to_activity_id', $activityIds);
      $q->orderBy('in_reply_to_activity_id, created_at DESC');
      $searchResult = $q->fetchArray();

      $queryCacheHash = $q->calculateQueryCacheHash();
    }
    else
    {
      $q->setCachedQueryCacheHash($queryCacheHash);
      $searchResult = $q->fetchArray();
    }

    $replaies = array();
    foreach ($searchResult as $row)
    {
      $targetId = $row['in_reply_to_activity_id'];

      if (!isset($replaies[$targetId]) || count($replaies[$targetId]) < self::COMMENT_DISPLAY_MAX)
      {
        $replaies[$targetId][] = $row;
      }

    }

    return $replaies;
  }

  public function findMemberNamesByMemberIds(array $memberIds)
  {
    static $queryCacheHash;

    $q = Doctrine_Query::create();

    if (!$queryCacheHash)
    {
      $q = Doctrine_Query::create();
      $q->from('Member m');
      $q->select("name");

      $q->whereIn('id', $memberIds);
      $searchResult = $q->fetchArray();

      $queryCacheHash = $q->calculateQueryCacheHash();
    }
    else
    {
      $q->setCachedQueryCacheHash($queryCacheHash);
      $searchResult = $q->fetchArray();
    }

    $names = array();
    foreach ($searchResult as $row)
    {
      $names[$row['id']] = $row['name'];
    }

    return $names;
  }

  /**
   *
   * @param array $memberIds
   * @return array (member_id => freind_count)
   */
  public function findFriendCountByMemberIds(array $memberIds)
  {
    static $queryCacheHash;

    $q = Doctrine_Query::create();

    if (!$queryCacheHash)
    {

      //innerjoinをするとエラーが出てしまったので、２回SQLを実行する
      $inactiveIds = Doctrine::getTable('Member')->getInactiveMemberIds();

      $q = Doctrine_Query::create();
      $q->from('MemberRelationship mr');
      $q->select("member_id_to as member_id, COUNT('*')");

      $q->whereIn('member_id_to', $memberIds);
      $q->AndWhereNotIn('member_id_from', $inactiveIds);
      $q->groupBy('member_id_to');

      $searchResult = $q->fetchArray();

      $queryCacheHash = $q->calculateQueryCacheHash();
    }
    else
    {
      $q->setCachedQueryCacheHash($queryCacheHash);
      $searchResult = $q->fetchArray();
    }

    $friendCounts = array();
    foreach ($searchResult as $row)
    {
      $friendCounts[$row['member_id']] = $row['COUNT'];
    }

    return $friendCounts;
  }

  public function findIntroductionIdFromProfile()
  {
    static $queryCacheHash;

    $q = Doctrine_Query::create();

    if (!$queryCacheHash)
    {
      $q->from('Profile');
      $q->select('id');
      $q->where("name = 'op_preset_self_introduction'");

      $searchResult = $q->fetchArray();
      $queryCacheHash = $q->calculateQueryCacheHash();
    }
    else
    {
      $q->setCachedQueryCacheHash($queryCacheHash);
      $searchResult = $q->fetchArray();
    }

    if (empty($searchResult))
    {
      return false;
    }

    return $searchResult[0]['id'];
  }

  /**
   *
   * @return array
   *  (memberId => Introducton)
   */
  public function findMemberIntroductionByMemberIdsAndIntroductionId(array $memberIds, $introductionId)
  {
    static $queryCacheHash;

    $q = Doctrine_Query::create();

    if (!$queryCacheHash)
    {
      $q->from('MemberProfile');
      $q->select('value, member_id');
      $q->where('profile_id = ?', $introductionId);
      $q->andWhereIn('member_id', $memberIds);

      $searchResult = $q->fetchArray();
      $queryCacheHash = $q->calculateQueryCacheHash();
    }
    else
    {
      $q->setCachedQueryCacheHash($queryCacheHash);
      $searchResult = $q->fetchArray();
    }

    $profiles = array();
    foreach ($searchResult as $row)
    {
      $profiles[$row['member_id']] = $row['value'];
    }

    return $profiles;
  }

  /**
   *
   * @return array
   *   (memberId => imagePath...)
   */
  public function findImageFileUrlsByMemberIds($memberIds)
  {
    static $queryCacheHash;

    $q = Doctrine_Query::create();

    if (!$queryCacheHash)
    {
      $q->from('MemberImage mi, mi.File f');
      $q->select('mi.member_id, f.name');

      $searchResult = $q->fetchArray();
      $queryCacheHash = $q->calculateQueryCacheHash();
    }
    else
    {
      $q->setCachedQueryCacheHash($queryCacheHash);
      $searchResult = $q->fetchArray();
    }

    $imageUrls = array();

    foreach ($searchResult as $row)
    {
      $image = sf_image_path($row['File']['name'], array('size' => '48x48'), true);
      $imageUrls[$row['member_id']] = $image;
    }

    //画像を設定していないユーザーはno_imageにする
    foreach ($memberIds as $id)
    {
      if (!isset($imageUrls[$id]))
      {
        $imageUrls[] = op_image_path('no_image.gif', true);
      }
    }

    return $imageUrls;
  }

  /**
   *
   * @return array
   *   freind => array(memberId...)
   *   block  => array(memberId...)
   */
  public function findFriendMemberIdsAndBlockMemberIdsByMemberId($memberId)
  {
    static $queryCacheHash;

    $q = Doctrine_Query::create()->from('MemberRelationship mr');
    $q->select('member_id_from, is_friend, is_access_block');

    $q->where('member_id_to = ?', $memberId);
    $q->andWhere('is_friend = 1 OR is_access_block = 1 ');

    if (!$queryCacheHash)
    {
      $searchResult = $q->fetchArray();
      $queryCacheHash = $q->calculateQueryCacheHash();
    }
    else
    {
      $q->setCachedQueryCacheHash($queryCacheHash);

      $searchResult = $q->fetchArray();
    }


    $friendIds = array();
    $blockIds = array();

    foreach ($searchResult as $row)
    {
      if ($row['is_friend'])
      {
        $friendIds[] = $row['member_id_from'];
      }

      if ($row['is_access_block'])
      {
        $blockIds[] = $row['member_id_from'];
      }
    }

    return array('friend' => $friendIds, 'block' => $blockIds);
  }

  public function formattedActivityDataByActivityData(array $activityData)
  {
    
  }

  public function searchActivityDatasByAPIRequestDatasAndMemberId($requestDatas, $memberId)
  {
    $builder = opActivityQueryBuilder::create()
                    ->setViewerId($memberId);

    if (isset($requestDatas['target']))
    {
      if ('friend' === $requestDatas['target'])
      {
        $builder->includeFriends($requestDatas['target_id'] ? $requestDatas['target_id'] : null);
      }

      if ('community' === $requestDatas['target'])
      {
        $builder
                ->includeSelf()
                ->includeFriends()
                ->includeSns()
                ->setCommunityId($requestDatas['target_id']);
      }
    }
    else
    {
      if (isset($requestDatas['member_id']))
      {
        $builder->includeMember($requestDatas['member_id']);
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

    if (isset($requestDatas['keyword']))
    {
      $query->andWhereLike('body', $requestDatas['keyword']);
    }

    $globalAPILimit = sfConfig::get('op_json_api_limit', 20);
    if (isset($requestDatas['count']) && (int) $requestDatas['count'] < $globalAPILimit)
    {
      $query->limit($requestDatas['count']);
    }
    else
    {
      $query->limit($globalAPILimit);
    }

    if (isset($requestDatas['max_id']))
    {
      $query->addWhere('id <= ?', $requestDatas['max_id']);
    }

    if (isset($requestDatas['since_id']))
    {
      $query->addWhere('id > ?', $requestDatas['since_id']);
    }

    if (isset($requestDatas['activity_id']))
    {
      $query->addWhere('id = ?', $requestDatas['activity_id']);
    }

    $query->andWhere('in_reply_to_activity_id IS NULL');

    return $query->execute();
  }

  /**
   *
   * @todo 削除する
   */
  public function getPublicStatusListByIds($ids)
  {
    $query = new opDoctrineQuery();

    $query->select('id, public_flag');
    $query->from('ActivityData');
    $query->andWhereIn('id', $ids);

    $fetchData = $query->fetchArray();


    $publicFlags = array();
    foreach ($fetchData as $data)
    {
      $publicStatues[$data['id']] = $publicStatusTextList[(int) $data['public_flag']];
    }

    return $publicStatues;
  }

  public function findActivityImageUrlsByActivityIds(array $actvityIds)
  {
    $query = new opDoctrineQuery();
    $query->select('activity_data_id, uri');
    $query->from('ActivityImage');
    $query->andWhereIn('activity_data_id', $actvityIds);

    $searchResult = $query->fetchArray();

    $imageUrls = array();
    foreach ($searchResult as $row)
    {
      $imageUrls[$row['activity_data_id']] = $row['uri'];
    }

    return $imageUrls;
  }

  public function addImageUrlToContentForSearchAPI(array $responseDatas)
  {

    $imageUrls = array();
    foreach ($responseDatas as $row)
    {
      $imageUrls[$row['id']] = $row['image_url'];
    }

    foreach ($responseDatas as &$data)
    {
      $id = $data['id'];

      if (isset($imageUrls[$id]))
      {
        $data['body'] = $data['body'].' '.$imageUrls[$id];
        $data['body_html'] = $data['body_html'].'<a href="'.$imageUrls[$id].'" rel="lightbox"><div><img src="'.$imageUrls[$id].'"></div></a>';
      }
    }
    unset($data);

    return $responseDatas;
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
