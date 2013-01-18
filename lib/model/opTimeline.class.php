<?php

class opTimeline
{

  /**
   * @var opTimelineUser
   */
  private $_user;

  private $_imageContentSize;
  private $_baseUrl;


  public function __construct(opTimelineUser $user, array $params)
  {
    $this->_user = $user;

    $this->_imageContentSize = $params['image_size'];
    $this->_baseUrl = $params['base_url'];
  }

  const COMMENT_DISPLAY_MAX = 10;
  const MINIMUM_IMAGE_WIDTH = 285;

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
   */
  public function createActivityDatasByActivityDataAndViewerMemberIdForSearchAPI($activityDatas, $viewerMemberId)
  {
    $activityIds = array();
    foreach ($activityDatas as $activity)
    {
      $activityIds[] = $activity->getId();
    }

    if (empty($activityIds))
    {
      return array();
    }

    $replayActivityDatas = $this->findReplayActivityDatasByActivityIdsGroupByActivityId($activityIds);

    $memberIds = $this->_extractionMemberIdByActivitieyDatasAndReplayActivityDataRows(
                    $activityDatas, $replayActivityDatas);
    $memberDatas = $this->_user->createMemberDatasByViewerMemberIdAndMemberIdsForAPIResponse($viewerMemberId, $memberIds);

    $responseDatas = $this->_createActivityDatasByActivityDatasAndMemberDatasForSearchAPI($activityDatas, $memberDatas);

    foreach ($responseDatas as &$response)
    {
      $id = $response['id'];

      if (isset($replayActivityDatas[$id]))
      {
        $replaies = $replayActivityDatas[$id];

        $response['replies'] = $this->_createActivityDatasByActivityDataRowsAndMemberDatasForSearchAPI($replaies['data'], $memberDatas);
        $response['replies_count'] = $replaies['count'];
      }
      else
      {
        $response['replies'] = null;
        $response['replies_count'] = 0;
      }
      $response['body_html'] = htmlspecialchars($response['body_html'], ENT_QUOTES, 'UTF-8', false);
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

    foreach ($replayActivitiyRows as $activityDatas)
    {
      foreach ($activityDatas['data'] as $activityData)
      {
        $memberIds[] = $activityData['member_id'];
      }
    }

    $memberIds = array_unique($memberIds);

    return $memberIds;
  }

  private function _createActivityDatasByActivityDatasAndMemberDatasForSearchAPI($activityDatas, $memberDatas)
  {
    $activityIds = array();
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

      $imageUrls = $this->_getImageUrlInfoByImageUrl($activityImageUrl);

      $responseData['id'] = $activity->getId();
      $responseData['member'] = $memberDatas[$activity->getMemberId()];

      $responseData['body'] = $activity->getBody();
      $responseData['body_html'] = op_activity_linkification(nl2br(op_api_force_escape($activity->getBody())));
      $responseData['uri'] = $activity->getUri();
      $responseData['source'] = $activity->getSource();
      $responseData['source_uri'] = $activity->getSourceUri();

      $responseData['image_url'] = $imageUrls['small'];
      $responseData['image_large_url'] = $imageUrls['large'];
      $responseData['created_at'] = date('r', strtotime($activity->getCreatedAt()));

      $responseDatas[] = $responseData;
    }

    return $responseDatas;
  }

  private function _getImageUrlInfoByImageUrl($imageUrl)
  {
    if ($imageUrl === null)
    {
      return array(
          'large' => null,
          'small' => null,
      );
    }

    $imagePath = $this->_convertImageUrlToImagePath($imageUrl);

    if (!file_exists($imagePath))
    {
      return array(
          'large' => opTimelineImage::getNotImageUrl(),
          'small' => opTimelineImage::getNotImageUrl(),
      );
    }

    $minimumDirPath = opTimelineImage::findUploadDirPath($imagePath, self::MINIMUM_IMAGE_WIDTH);
    $imageName = pathinfo($imagePath, PATHINFO_BASENAME);
    $minimumImagePath = $minimumDirPath.'/'.$imageName;

    if (!file_exists($minimumImagePath))
    {
      return array(
          'large' => $imageUrl,
          'small' => $imageUrl,
      );
    }

    $minimumImageUrl = str_replace(sfConfig::get('sf_web_dir'), $this->_baseUrl, $minimumImagePath);

    return array(
        'large' => $imageUrl,
        'small' => $minimumImageUrl,
    );
  }

  private function _convertImageUrlToImagePath($imageUrl)
  {
    $match = array();
    preg_match("/(http:\/\/.*)(\/cache)/", $imageUrl, $match);

    return str_replace($match[1], sfConfig::get('sf_web_dir'), $imageUrl);
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

      if (!isset($replaies[$targetId]['data']) || count($replaies[$targetId]['data']) < self::COMMENT_DISPLAY_MAX)
      {
        $replaies[$targetId]['data'][] = $row;
      }

      if (isset($replaies[$targetId]['count']))
      {
        $replaies[$targetId]['count']++;
      }
      else
      {
        $replaies[$targetId]['count'] = 1;
      }
    }

    return $replaies;
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

  public function embedImageUrlToContentForSearchAPI(array $responseDatas)
  {
    $imageUrls = array();
    foreach ($responseDatas as $row)
    {
      if ($row['image_url'] !== null)
      {
        if ($this->_imageContentSize === 'large')
        {
          $imageUrls[$row['id']] = $row['image_large_url'];
        }
        else
        {
          $imageUrls[$row['id']] = $row['image_url'];
        }
        
      }
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
   */
  public function createActivityImageByFileInfoAndActivityId(array $fileInfo, $activityId)
  {
    $file = new File();
    $file->setOriginalFilename(basename($fileInfo['name']));
    $file->setType($fileInfo['type']);

    $fileBaseName = md5(time()).'_'.$file->getImageFormat();
    $filename = 'ac_'.$fileInfo['member_id'].'_'.$fileBaseName;

    $file->setName($filename);
    $file->setFilesize($fileInfo['size']);
    $bin = new FileBin();
    $bin->setBin($fileInfo['binary']);
    $file->setFileBin($bin);
    $file->save();

    $activityImage = new ActivityImage();
    $activityImage->setActivityDataId($activityId);
    $activityImage->setFileId($file->getId());
    $activityImage->setUri($this->_getActivityImageUriByfileInfoAndFilename($fileInfo, $filename));
    $activityImage->setMimeType($file->type);
    $activityImage->save();

    $this->_createUploadImageFileByFileInfoAndSaveFileName($fileInfo, $filename);

    return $activityImage;
  }

  private function _getActivityImageUriByfileInfoAndFilename($fileInfo, $filename)
  {
    //ファイルテーブルの名前だと拡張式がついていない
    $filename = opTimelineImage::addExtensionToBasenameForFileTable($filename);
    $uploadPath = opTimelineImage::findUploadDirPath($filename);
    $uploadBasePath = str_replace(sfConfig::get('sf_web_dir'), '', $uploadPath);

    return $fileInfo['web_base_path'].$uploadBasePath.'/'.$filename;
  }

  private function _createUploadImageFileByFileInfoAndSaveFileName($fileInfo, $filename)
  {
    $filename = opTimelineImage::addExtensionToBasenameForFileTable($filename);
    $uploadDirPath = opTimelineImage::findUploadDirPath($fileInfo['name']);

    $fileSavePath = $uploadDirPath.'/'.$filename;

    opTimelineImage::copyByResourcePathAndTargetPath($fileInfo['tmp_name'], $fileSavePath);

    $imageSize = opTimelineImage::getImageSizeByPath($fileSavePath);
    //画像が縮小サイズより小さい場合は縮小した画像を作成しない
    if ($imageSize['width'] <= self::MINIMUM_IMAGE_WIDTH)
    {
      return true;
    }

    $minimumDirPath = opTimelineImage::findUploadDirPath($fileInfo['name'], self::MINIMUM_IMAGE_WIDTH);
    $minimumPath = $minimumDirPath.'/'.basename($fileSavePath);

    $paths = array(
        'resource' => $fileSavePath,
        'target' => $minimumPath,
    );

    opTimelineImage::createMinimumImageByWidthSizeAndPaths(self::MINIMUM_IMAGE_WIDTH, $paths);
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
