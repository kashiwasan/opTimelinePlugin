<script type="text/javascript">
  $(document).ready(function(){      
    $("textarea").focus(function(){
       $(this).addClass("expand");
       if($(this).val()=="今なにしてる？")
       {
         $(this).val("");
       }
    }).blur(function(){
       if($(this).val()=="")
       {
         $(this).removeClass("expand");
         $(this).val("今なにしてる？");
       }
    });
  }); 
</script>

<script id="timelineTemplate" type="text/x-jquery-tmpl">
    <div class="gorgon-list">
      <div class="gorgon-img"> <img height="48" width="48" src="${memberImage}" alt="${memberScreenName}"> </div>
      <div class="gorgon">
        <div class="gorgon-row">
          <div class="gorgon-text"><a class="gorgon-screenname" href="<?php echo url_for('@homepage', array('absolute' => true)); ?>/member/${memberId}">${memberScreenName}</a> {{html body}} </div>
        </div>
        <div class="gorgon-row"> <div class="_reply-link" style="margin-right: 15px; display: inline;"><a href="#" class="timestamp"><span class="_timestamp"><img src="<?php echo url_for('@homepage', array('absolute' => true)); ?>/opTimelinePlugin/css/images/clock.png" alt="timelineTimestampIcon" width="15" height="15" /> ${createdAt}</span></a></div> <div class="delteLink" style="display: ${deleteLink};"><a href="#" rel="timelineDelete" class="timestamp" location-url="<?php echo url_for('@homepage', array('absolute' => true)); ?>" data-activity-id="${id}" data-activity-body="${convertTag(body)}" data-activity-memberScreenName="${memberScreenName}" data-activity-csrftoken="<?php echo $token; ?>"><img src="<?php echo url_for('@homepage', array('absolute' => true)); ?>/opTimelinePlugin/css/images/trash_can.png" alt="timelineDeleteIcon" width="15" height="15" /> Delete</a></div></div>
      </div>
      <div class="comment-list" id="commentlist-${id}">
        <form style="margin-bottom: 0px;"><textarea data-timeline-id="${id}" data-post-csrftoken="<?php echo $token; ?>" style="height: 35px; width: 328px;" id="comment-textarea-${id}"></textarea><button data-timeline-id="${id}" data-post-csrftoken="<?php echo $token; ?>" data-post-baseurl="<?php echo url_for('@homepage', array('absolute' => true)); ?>" class="btn primary small timeline-comment-button" style="height: 20px; width: 328px; padding: 1px;">投稿</button></form>
      </div>
    </div>
</script>

<script id="timelineCommentTemplate" type="text/x-jquery-tmpl">
        <div class="comment-img"><img src="${memberImage}" width="32" height="32" alt="${memberScreenName}-comment-image"></div>
        <div class="comment">
          <div class="comment-row">
            <div class="comment-text"><a class="comment-screenname">${memberScreenName}</a> {{html body}}</div>
          </div>
          <div class="comment-row"> <div class="_reply-link" style="margin-right: 15px; display: inline;"><a href="#" class="timestamp"><span class="_timestamp"><img src="<?php echo url_for('@homepage', array('absolute' => true)); ?>/opTimelinePlugin/css/images/clock.png" alt="timelineTimestampIcon" width="15" height="15" /> ${createdAt}</span> - Like!</a></div> <div class="delteLink" style="display: ${deleteLink};"><a href="#" rel="timelineDelete" class="timestamp" location-url="<?php echo url_for('@homepage', array('absolute' => true)); ?>" data-activity-id="${id}" data-activity-body="${convertTag(body)}" data-activity-memberScreenName="${memberScreenName}" data-activity-csrftoken="<?php echo $token; ?>"><img src="<?php echo url_for('@homepage', array('absolute' => true)); ?>/opTimelinePlugin/css/images/trash_can.png" alt="timelineDeleteIcon" width="15" height="15" /> Delete</a></div></div>
        </div>
</script>

<script id="timeline-error-template" type="text/x-jquery-tmpl">
  <div id="error-panel" style="display: none;">
    <span id="error">${json.message}</span>
  </div>
</script>
<script id="timeline-delete-confirm-template" type="text/x-jquery-tmpl">
  <div id="delete-confirm-panel" style="display: none;">
   <span id="error">${json.message}</span>
  </div>
</script>
<div class="partsHeading"><h3>SNSメンバー全員のタイムライン</h3></div>
<a href="<?php echo url_for('@homepage', array('absolute' => true)); ?>/member/config?category=timelineScreenName">■スクリーンネーム設定画面</a><br />

<div class="main-content" style="min-height: 500px; ">
  <div class="page-header">
    <div id="main-gorgon-box">
      <div class="gorgon-box">
          <textarea class="gorgon-textarea" id="gorgon-textarea-body">今なにしてる？</textarea>
          <button class="gorgon-button button" id="gorgon-submit" data-post-csrftoken="<?php echo $token; ?>" data-post-baseurl="<?php echo url_for('@homepage', array('absolute' => true)); ?>">投稿</button>
      </div>
    </div>
    <div id="timeline-list" data-post-csrftoken="<?php echo $token; ?>" data-post-baseurl="<?php echo url_for('@homepage', array('absolute' => true)); ?>">
    </div>
  </div>
</div>
