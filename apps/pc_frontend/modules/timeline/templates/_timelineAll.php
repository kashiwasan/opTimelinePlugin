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
          <div class="gorgon-text"><a class="gorgon-screenname" href="<?php echo $baseUrl; ?>/member/${memberId}">${memberScreenName}</a> {{html body}} </div>
        </div>
        <div class="gorgon-row"> <div class="reply-link"><a href="#" class="timestamp"><span class="_timestamp"> ${createdAt}</span></a></div> <div class="delete-link" style="display: ${deleteLink};"><a href="#" rel="timelineDelete" class="timestamp" location-url="<?php echo $baseUrl ?>" data-activity-id="${id}" data-activity-body="${convertTag(body)}" data-activity-memberScreenName="${memberScreenName}" data-activity-csrftoken="<?php echo $token; ?>"> Delete</a></div></div>
      </div>
      <div class="comment-list" id="commentlist-${id}">
      </div>
    </div>
</script>

<script id="timelineCommentTemplate" type="text/x-jquery-tmpl">
        <div class="comment-img"><img src="${memberImage}" width="32" height="32" alt="${memberScreenName}-comment-image"></div>
        <div class="comment">
          <div class="comment-row">
            <div class="comment-text"><a class="comment-screenname" href="<?php echo $baseUrl; ?>/member/${memberId}">${memberScreenName}</a> {{html body}}</div>
          </div>
          <div class="comment-row"> <div class="reply-link"><a href="#" class="timestamp"><span class="_timestamp"> ${createdAt}</span></a></div> <div class="delete-link" style="display: ${deleteLink};"><a href="#" rel="timelineDelete" class="timestamp" location-url="<?php echo $baseUrl ?>" data-activity-id="${id}" data-activity-body="${convertTag(body)}" data-activity-memberScreenName="${memberScreenName}" data-activity-csrftoken="<?php echo $token; ?>"> Delete</div></div>
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

<a href="<?php echo $baseUrl; ?>/member/config?category=timelineScreenName">■スクリーンネーム設定画面</a><br />

<div class="main-content" style="min-height: 500px; max-width: 440px;">
  <div class="page-header">
    <div id="main-gorgon-box">
      <div class="gorgon-box">
          <textarea class="gorgon-textarea" id="gorgon-textarea-body">今なにしてる？</textarea>
          <button class="gorgon-button button" id="gorgon-submit" data-post-csrftoken="<?php echo $token; ?>" data-post-baseurl="<?php echo $baseUrl; ?>">投稿</button>
      </div>
    </div>
    <div id="timeline-list">
    </div>
  </div>
</div>

