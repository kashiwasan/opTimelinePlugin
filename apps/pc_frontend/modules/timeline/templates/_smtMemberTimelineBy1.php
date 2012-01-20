<?php use_helper('opUtil'); ?>
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
<script type="text/javascript">
//<![CDATA[
var gorgon = {
      'mode': 'member',
      'memberId': <?php echo $memberId; ?>, 
      'post': {
      }
    };
//]]>
</script>
<script type="text/javascript" src="<?php echo url_for('@homepage'); ?>opTimelinePlugin/js/jquery.timeline.js"></script>
<script type="text/javascript" src="<?php echo url_for('@homepage'); ?>opTimelinePlugin/js/gorgon-smt.js"></script>
