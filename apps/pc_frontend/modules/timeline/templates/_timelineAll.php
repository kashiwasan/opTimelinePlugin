<script type="text/javascript">
//<![CDATA[
var gorgon = {
      'limit': '20',
      'post': {

      },
      'timer': '60000',
    };
//]]>
</script>

<?php use_javascript('/opTimelinePlugin/js/jquery.ui.widget.js') ?>
<?php use_javascript('/opTimelinePlugin/js/jquery.iframe-transport.js') ?>
<?php use_javascript('/opTimelinePlugin/js/jquery.fileupload.js') ?>
<?php use_javascript('/opTimelinePlugin/js/bootstrap-tooltip.js') ?>
<?php use_javascript('/opTimelinePlugin/js/timeline-loader.api.js') ?>
<?php use_javascript('/opTimelinePlugin/js/counter.js') ?>
<?php use_stylesheet('/opTimelinePlugin/css/bootstrap.css', 'last') ?>
<?php use_stylesheet('/opTimelinePlugin/css/timeline.css', 'last') ?>
<?php use_stylesheet('/opTimelinePlugin/css/counter.css', 'last') ?>

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

<div class="dparts">
  <div class="parts">
<div class="partsHeading"><h3>SNSメンバー全員のタイムライン</h3></div>

    <div class="timeline">
      <div class="timeline-postform well">
        <textarea id="timeline-textarea" class="input-xlarge" rows="1" placeholder="今何してる？"></textarea>
        <div id="timeline-submit-loader">
          <?php echo op_image_tag('ajax-loader.gif', array()) ?>
        </div>
        <div class="progress progress-info progress-striped active" id="timeline-progress">
          <div class="bar" id="timeline-progress-bar" style="width: 0%;"></div>
        </div>
        <div id="timeline-submit-error"></div>
        <div id="timeline-submit-area" style="height: 30px; position: relative;">
          <input type="file" id="timeline-upload" name="images" />
          <span class="btn" id="timeline-upload-button"><i class="icon-picture"></i></span>      
          <button id="timeline-submit-button" class="btn btn-primary timeline-submit">投稿</button>
        </div>
      </div>

      <div id="timeline-loading" style="text-align: center;"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
      <div id="timeline-list" data-last-id=""data-loadmore-id="">

      </div>
      <button class="btn btn-small" id="timeline-loadmore" style="width: 100%;">もっと読む</button>
      <div id="timeline-loadmore-loading"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
    </div>
  </div>

</div>

