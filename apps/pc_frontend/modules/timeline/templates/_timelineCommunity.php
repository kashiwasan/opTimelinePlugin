<script type="text/javascript">
//<![CDATA[
var gorgon = {
      'target': 'community',
      'target_id': <?php echo $community->getId(); ?>,
      'count': '20',
      'post': {
        'foreign': 'community',
        'foreignId': '<?php echo $community->getId(); ?>',
      },
      'notify': {
        'lib': '<?php echo url_for('@homepage', array('absolute' => true)); ?>opTimelinePlugin/js/jquery.desktopify.js',
        'title': '<?php echo $community->getName();?> の最新投稿',
        <?php if ($community->getImageFileName()): ?>
        'icon': '<?php echo sf_image_path($community->getImageFileName(), array('size' => '48x48',)); ?>',
        <?php endif; ?>
      },
      'timer': '5000',
    };
//]]>
</script>

<?php use_javascript('/opTimelinePlugin/js/jquery.desktopify.js', 'last'); ?>
<?php use_javascript('/opTimelinePlugin/js/timeline-loader.api.js', 'last') ?>
<?php use_stylesheet('/opTimelinePlugin/css/bootstrap.css', 'last') ?>
<?php use_stylesheet('/opTimelinePlugin/css/timeline.css', 'last') ?>

<script type="text/javascript">
$(function(){
  $("#timeline-textarea").focus(function(){
    $('.timeline-postform').css('padding-bottom', '30px');
    $('#timeline-textarea').attr('rows', '3');
    $('#timeline-submit-area').css('display', 'inline');
  });
});
</script>
<?php include_partial('timeline/timelineTemplate') ?>
<div class="partsHeading"><h3>コミュニティタイムライン</h3></div>
    <div class="timeline">
      <div class="timeline-postform well">
        <textarea id="timeline-textarea" class="input-xlarge" rows="1" placeholder="今何してる？"></textarea>
        <div id="timeline-submit-loader"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
        <div id="timeline-submit-error"></div>
        <div id="timeline-submit-area">
          <button id="timeline-submit-button" class="btn btn-primary timeline-submit">投稿</button>
        </div>
      </div>

      <div id="timeline-loading" style="text-align: center;"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
      <div id="timeline-list" data-last-id=""data-loadmore-id="">
      </div>

      <button class="gorgon-button button" id="timeline-loadmore">もっと読む</button>
      <div id="timeline-loadmore-loading"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
    </div>

