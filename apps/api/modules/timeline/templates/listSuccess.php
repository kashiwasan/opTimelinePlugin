<?php

use_helper('Helper', 'Date', 'sfImage', 'opUtil');

$ac = array();
foreach ($activityData as $activity)
{
  $member = $activity->getMember();
  $memberImageFileName = $member->getImageFileName();
  if (!$memberImageFileName)
  {
    $memberImage = $baseUrl . '/images/no_image.gif';
  }
  else
  {
    $memberImage = sf_image_path($memberImageFileName, array('size' => '48x48'));
  }

  $ac[] = array(
    'id' => $activity->getId(),
    'memberId' => $member->getId(),
    'memberImage' => $memberImage,
    'memberScreenName' => $member->getConfig('op_screen_name', $member->getName()),
    'memberName' => $member->getName(),
    'body' => $activity->getBody(),
    'deleteLink' => $member->getId() == $viewMemberId ? 'inline' : 'none',
    'uri' => $activity->getUri(),
    'source' => $activity->getSource(),
    'sourceUri' => $activity->getSourceUri(),
    'createdAt' => op_format_activity_time(strtotime($activity->getCreatedAt())),
    'baseUrl' => $baseUrl,
  );
}

return array(
  'status' => 'success',
  'data' => $ac,
);
