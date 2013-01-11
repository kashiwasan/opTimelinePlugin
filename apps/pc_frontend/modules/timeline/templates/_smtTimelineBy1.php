<?php use_helper('opUtil', 'opTimeline'); ?>
<div class="row">
<div class="gadget_header span12">最新のタイムライン</div>
</div>
<div class="row">
  <div class="span12">
  <?php if (isset($createdAt) && isset($body)): ?>
  <?php echo op_format_activity_time(strtotime($createdAt)); ?> - <?php echo op_timeline_plugin_screen_name($body) ?>
  <?php else: ?>
  (タイムラインはまだありません。)
  <?php endif; ?>
  </div>
  <div class="span3 offset9">
  <?php echo link_to('もっと見る', '@sns_timeline'); ?>
  </div>
</div>
