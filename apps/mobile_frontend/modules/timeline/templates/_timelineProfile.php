<?php $list = array() ?>
<?php foreach ($activities as $activity): ?>
<?php $list[] = get_partial('timeline/timelineRecord', array('activity' => $activity)) ?>
<?php endforeach; ?>

<?php $params = array(
  'title' => isset($title) ? $title : $member->getName().'のﾀｲﾑﾗｲﾝ',
  'list' => $list,
  'border' => true,
) ?>
<?php op_include_parts('list', 'ActivityBox', $params) ?>
