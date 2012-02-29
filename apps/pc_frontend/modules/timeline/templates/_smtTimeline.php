<?php use_helper('Javascript', 'opUtil') ?>
<script type="text/javascript">
//<![CDATA[
var gorgon = {
      'mode': 'all',
      'post': {
      }
    };
//]]>
</script>
<?php use_stylesheet('/opTimelinePlugin/css/colorbox.css') ?>
<?php use_javascript('/opTimelinePlugin/js/jquery.colorbox.js', 'last') ?>
<?php use_javascript('/opTimelinePlugin/js/jquery.timeline.js', 'last') ?>
<?php use_javascript('/opTimelinePlugin/js/gorgon-smt.js', 'last') ?>

<script id="timelineTemplate" type="text/x-jquery-tmpl">
<div class="timeline font14 row">
  <div class="span2"><a href="<?php echo url_for('@homepage', array('absolute' => true)); ?>member/${member.id}"><img src="${member.profile_image}" class="rad6" width="46" height="46" /></a></div>
  <div class="span10">
    <div id="timelinebody-${id}" class="span10" style="min-height: 48px; max-width: 230px;">
    <b><a href="${member.profile_url}">{{if member.screen_name}} ${member.screen_name} {{else}} ${member.name} {{/if}}</a></b><div class="timelinebody-context" id="timelinebody-context-${id}">{{html body}}</div></div>
    <div id="commentlist-${id}"></div>
  </div>
</div>
<div class="row span10" id="timeline-now-comment-${id}" style="margin-left: 50px;">
  <button class="timeline-now-comment-button commentbutton btn span10 btn-mini" style="height: 20px; padding-top: 2px; padding-bottom: 2px; margin-left: 0px; margin-right: 0px; padding-left: 0px; padding-right: 0px;">コメントする</button>
</div>
<div class="row hide" id="timeline-comment-form-${id}">
  <form class="span10 offset2" style="margin-bottom: 0px;">
    <textarea class="span10 comment-textarea" data-timeline-id="${id}" style="height: 35px;" id="comment-textarea-${id}"></textarea>
    <button data-timeline-id="${id}" class="btn btn-primary btn-mini timeline-comment-button center span10" style="height: 20px; padding: 1px; text-align: center;">投稿</button>
  </form>
</div>
</script>

<script id="timelineCommentTemplate" type="text/x-jquery-tmpl">
<div class="comment font12 row">
  <div class="span10">
    <div class="row">
      <div class="span1"><a href="${member.profile_url}"><img src="${member.profile_image}" class="rad6" width="23" height="23" /></a></div>
      <div class="span9">
        <b><a href="${member.profile_image}">{{if member.screen_name}} ${member.screen_name} {{else}} ${member.name} {{/if}}</a></b> 
        <span id="timeline-comment-context-${id}" class="timeline-comment-context">{{html body}}</span>
      </div>
    </div>
  </div>
</div>
</script>

<div style="display: none;">
<div id="timeline-warning">
  <div class="modal-header">
    <h3>投稿エラー</h3>
  </div>
  <div class="modal-body">
    <p>本文が入力されていません</p>
  </div>
</div>
</div>

<div class="row">
  <div class="gadget_header span12">SNS全体のタイムライン</div>
</div>

<div id="timeline-list" class="row span12 hide" data-last-id="" data-loadmore-id="">
</div>
<div id="timeline-list-loader" class="row span12 center show" style="margin-top: 20px; margin-bottom: 20px;">
<?php echo op_image_tag('ajax-loader.gif', array('alt' => 'Now Loading...')) ?>
</div>
<div id="gorgon-submit"></div>
<hr class="toumei">
<div class="row">
  <button class="span12 btn small" id="gorgon-loadmore">もっと読む</button>
</div>

