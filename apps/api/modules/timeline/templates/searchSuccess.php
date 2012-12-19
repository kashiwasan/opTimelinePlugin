<?php
use_helper('opTimeline');

$ac = array();

foreach ($activityData as $activity)
{
  $acEntity = op_timeline_activity($activity);

  $replies = $activity->getReplies();
  if (0 !== count($replies))
  {
    $acEntity['replies'] = array();

    foreach ($replies as $reply)
    {
      $acEntity['replies'][] = op_api_activity($reply);
    }
  }

  $ac[] = $acEntity;
}

return array(
  'status' => 'success',
  'data' => $ac,
);
