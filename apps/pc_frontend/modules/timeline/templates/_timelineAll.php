<div id="homeAllTimeline_<?php echo $gadget->id ?>" class="dparts homeAllTimeline"><div class="parts">

<script type="text/javascript">
//<![CDATA[
var gorgon = {
      'count': '<?php echo $gadget->getConfig('limit'); ?>',
      'post': {

      },
      'timer': '60000'
    };
var MAXLENGTH = 140;
var viewPhoto = 1;

//]]>
</script>

<?php use_javascript('/opTimelinePlugin/js/jquery.upload-1.0.2.js') ?>
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

<div class="partsHeading"><h3>SNSメンバー全員のタイムライン</h3></div>

    <div class="timeline">
      <div class="timeline-postform well">
        <textarea id="timeline-textarea" class="input-xlarge" rows="1" placeholder="今何してる？" onkeypress="return (this.value.length < 1139)" onkeyup="lengthCheck(this);"></textarea>
        <div id="timeline-submit-loader"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
        <div id="timeline-submit-error"></div>
        <div id="timeline-upload-error"></div>
        <div id="timeline-submit-area">
          <span id="timeline-upload-photo-button" class="btn"><i class="icon-camera"></i></span>
          <span id="photo-file-name"></span>
          <span id="counter"></span>
          <select id="timeline-public-flag">
          <?php foreach ($publicFlags as $value => $text): ?>
            <option value="<?php echo $value ?>"><?php echo __($text) ?></option>
          <?php endforeach; ?>
          </select>
          <input id="timeline-submit-upload" type="file" name="timeline-submit-upload" enctype="multipart/form-data">
          <button id="timeline-submit-button" class="btn btn-primary timeline-submit">投稿</button>
        </div>
      </div>

      <div id="timeline-loading" style="text-align: center;"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
      <div id="timeline-list" data-last-id=""data-loadmore-id="">

      </div>
      <button class="btn btn-small" id="timeline-loadmore" style="width: 100%;">もっと読む</button>
      <div id="timeline-loadmore-loading"><?php echo op_image_tag('ajax-loader.gif', array()) ?></div>
    </div>

</div></div>
