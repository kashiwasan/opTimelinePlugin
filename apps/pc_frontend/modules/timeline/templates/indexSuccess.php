<script id="timelineTemplate" type="text/x-jquery-tmpl">
  <li class="stream-body">
    <div class="stream-image"><img src="${memberImage}" width="36" height="36" /></div>
    <div class="stream-right">
      <div class="stream-membername">${memberScreenName}</div>
      <div class="stream-text">${body}
      <div id="stream-util"><a href="#" id="timeline-reply" rel="${memberScreenName}">Reply</a> | <a href="/op3/timeline/deleteConfirm/${id}" id="timeline-delete">Delete</a> </div>
      <ul id="streamListComment${id}" class="commentList"></ul>
      <div class="stream-comment-form"><form><input type="text" id="stream-comment-input" /><input type="hidden" name="replyId" value="${id}" /></form></div>
      </div>
    </div>
  </li>
</script>

<script id="timelineCommentTemplate" type="text/x-jquery-tmpl">
  <li class="stream-comment-body">
    <div class="stream-membername">${memberScreenName}</div>
    <div class="stream-text">${body}
      <div id="stream-util"><a href="/op3/timeline/deleteConfirm/${id}" id="timeline-delete">Delete</a> </div>
    </div>
  </li>
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
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.js"></script>
<script type="text/javascript" src="/op3/opTimelinePlugin/js/timeline.js"></script>

<div id="timeline-container">
<div id="timeline-post">
  <div id="timeline-textbox"><textarea rows="8" cols="30" name="timeline-textarea" id="timeline-textarea"></textarea></div>
  <div id="timeline-submit"><button id="timeline-button">投稿する</button></div>
</div>
  <ul id="streamList">
  </ul>
</div>
