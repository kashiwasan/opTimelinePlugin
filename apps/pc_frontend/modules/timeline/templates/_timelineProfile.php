<script type="text/javascript">
//<![CDATA[
var gorgon = {
      'member_id': '<?php echo $memberId; ?>',
      'count': '20',
      'post': {

      },
      'timer': '120000',
    };
//]]>
</script>


<?php use_javascript('/opTimelinePlugin/js/timeline-loader.api.js', 'last') ?>
<?php use_stylesheet('/opTimelinePlugin/css/bootstrap.css', 'last') ?>
<?php use_stylesheet('/opTimelinePlugin/css/timeline.css', 'last') ?>

<?php include_partial('timeline/timelineTemplate') ?>

<div class="partsHeading"><h3>メンバーのタイムライン</h3></div>

<div class="timeline">
  <div style="display: none" id="timeline-submit-button" class="btn btn-primary timeline-submit"></div>
  <div id="timeline-loading" style="text-align: center;"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>

  <div id="timeline-list" data-last-id=""data-loadmore-id="">
  </div>
    
  <button class="gorgon-button button" id="timeline-loadmore">もっと読む</button>
  <div id="timeline-loadmore-loading"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
</div>
