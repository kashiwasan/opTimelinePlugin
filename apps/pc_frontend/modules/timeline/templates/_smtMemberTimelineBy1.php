<?php use_helper('opUtil', 'Javascript'); ?>
<div class="row">
<div class="gadget_header span12">最新のタイムライン</div>
</div>
<div class="row">
  <div class="span12">
  <?php if (isset($createdAt) && isset($body)): ?>
  <?php echo op_format_activity_time(strtotime($createdAt)); ?> - <?php echo $body; ?>
  <?php else: ?>
  (タイムラインはまだありません。)
  <?php endif; ?>
  </div>
  <div class="span3 offset9">
  <?php  if (isset($createdAt) && isset($body)): ?>
  <?php echo link_to('もっと見る', '@member_timeline?id='.$memberId); ?>
  <?php endif; ?>
  </div>
</div>
<?php
$gorgon = array(
  'mode' => 'member',
  'memberId' => $memberId,
  'post' => array(),
);
echo javascript_tag('
var gorgon = '.json_encode($gorgon, defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0).';
')
?>
<?php use_javascript('opTimelinePlugin/js/jquery.timeline.js', 'last') ?>
<?php use_javascript('opTimelinePlugin/js/gorgon-smt.js', 'last') ?>
