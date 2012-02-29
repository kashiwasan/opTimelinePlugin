<?php use_helper('opUtil', 'Javascript') ?>
<script type="text/javascript">
//<![CDATA[
var gorgon = {
      'member_id': <?php echo $id; ?>,
    };
//]]>
</script>
<?php use_stylesheet('/opTimelinePlugin/css/colorbox.css') ?>
<?php use_javascript('/opTimelinePlugin/js/jquery.colorbox.js', 'last') ?>
<?php use_javascript('/opTimelinePlugin/js/jquery.timeline.js', 'last') ?>
<script type="text/javascript" src="<?php echo url_for('@homepage', array('absolute' => true)) ?>opTimelinePlugin/js/gorgon-smt.js"></script>

<script id="timelineTemplate" type="text/x-jquery-tmpl">
<div class="timeline font10 row">
  <div class="span2"><a href="${member.profile_url}"><img src="${member.profile_image}" class="rad6" width="46" height="46" /></a></div>
  <div class="span10">
    <div id="timelinebody-${id}" style="min-height: 48px;">
    <b><a href="${member.profile_url}">{{if member.screen_name}} ${member.screen_name} {{else}} ${member.name} {{/if}}</a></b><br />
    {{html body}}
    </div>
    <div id="commentlist-${id}"></div>
  </div>
</div>
<div class="row span10" id="timeline-now-comment-${id}" style="margin-left: 50px;">
  <button class="timeline-now-comment-button commentbutton btn span10 small" style="height: 20px;  padding-top: 2px; padding-bottom: 2px; margin-left: 0px; margin-right: 0px; padding-left: 0px; padding-right: 0px;">コメントする</button>
</div>
<div class="row hide" id="timeline-comment-form-${id}">
  <form class="span10 offset2" style="margin-bottom: 0px;">
    <textarea class="span10" style="height: 35px;" id="comment-textarea-${id}"></textarea>
    <button data-timeline-id="${id}" class="btn primary small timeline-comment-button center span10" style="height: 20px; padding: 1px; text-align: center;">投稿</button>
  </form>
</div>
</script>

<script id="timelineCommentTemplate" type="text/x-jquery-tmpl">
<div class="comment font10 row">
  <div class="span10">
    <div class="row">
      <hr class="toumei2">
      <div class="span1"><a href="${member.profile_url}"><img src="${member.profile_image}" class="rad6" width="23" height="23" /></a></div>
      <div class="span9">
      <b><a href="${member.profile_image}">{{if member.screen_name}} ${member.screen_name} {{else}} ${member.name} {{/if}}</a></b><br />
      {{html body}}
      </div>
    </div>
  </div>
</div>
</script>

<div class="row">
  <div class="gadget_header span12"><?php if ($member): ?><?php echo $member->getName(); ?>さんのタイムライン<?php else: ?>タイムライン<?php endif; ?></div>
</div>
<?php if ($member): ?>
<?php if ($sf_user->getMemberId()==$member->getId()): ?>
<a href="<?php echo url_for('@homepage'); ?>member/config?category=timelineScreenName">■スクリーンネーム設定画面</a><br />
<?php endif; ?>
<?php endif; ?>

<div id="timeline-list" class="span12 hide" data-post-baseurl="<?php echo url_for('@homepage'); ?>" data-last-id="" data-loadmore-id="" style="margin-left: 0px;">
</div>
<div id="timeline-list-loader" class="row span12 center show" style="margin-top: 20px; margin-bottom: 20px;">
<?php echo op_image_tag('ajax-loader.gif', array('absolute' => true)) ?>
</div>
<div id="gorgon-submit" data-post-csrftoken="<?php echo $token; ?>" data-post-baseurl="<?php echo url_for('@homepage'); ?>"></div>
<div class="row">
  <button class="span12 btn small" id="gorgon-loadmore">もっと読む</button>
</div>

