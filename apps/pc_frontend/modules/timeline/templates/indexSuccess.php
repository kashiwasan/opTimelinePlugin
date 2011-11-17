<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.js"></script>
<script type="text/javascript" src="<?php echo $baseUrl; ?>/opTimelinePlugin/js/jquery.center.js"></script>
<script type="text/javascript" src="<?php echo $baseUrl; ?>/timeline/timelinePlugin"></script>
<script type="text/javascript" src="<?php echo $baseUrl; ?>/timeline/js"></script>

<script id="timelineCommentJquery" type="text/javascript">
</script>
<script id="timelineTemplate" type="text/x-jquery-tmpl">
  <li class="stream-body">
    <div class="stream-image"><a href="<?php echo $baseUrl; ?>/member/${memberId}" title="${memberScreenName}"><img src="${memberImage}" alt="${memberScreenName}" width="36" height="36" /></a></div>
    <div class="stream-right">
      <div class="stream-membername"><a href="<?php echo $baseUrl; ?>/member/${memberId}" title="${memberScreenName}">${memberScreenName}</a></div>
      <div class="stream-text">${body}
      <div id="stream-util"><a href="#" data-activity-id="${id}" rel="prettyPopin" class="timelineReplyLink">Reply</a> | <span id="timeline-delete-link-${id}" style="display: ${deleteLink};"><a data-activity-id="${id}" data-activity-body="${body}" data-activity-memberScreenName="${memberScreenName}" rel="timelineDelete" href="#" id="delete-timeline">削除する</a></span> (${createdAt})</div>
      <ul id="streamListComment${id}" class="commentList"></ul>
      </div>
    </div>
  </li>
</script>

<script id="timelineCommentTemplate" type="text/x-jquery-tmpl">
  <li class="stream-comment-body">
    <div class="stream-membername"><a href="<?php echo $baseUrl; ?>/member/${memberId}" title="${memberScreenName}">${memberScreenName}</a></div>
    <div class="stream-text">${body}
      <div id="stream-util"><span id="timeline-delete-link-${id}" style="display: ${deleteLink};"><a rel="timelineDelete" href="#" data-activity-id="${id}" data-activity-body="${body}" data-activity-memberScreenName="${memberScreenName}" id="detete-timeline">削除する</a></span> (${createdAt})</div>
    </div>
  </li>
</script>
<script id="timelineReplyPopup" type="text/x-jquery-tmpl">
<div id="stream-reply-popup">
  <div class="stream-reply-popup-header">
    <div class="stream-reply-popup-close">(x) 閉じる</div>
  </div>
  <div class="stream-reply-popup-body"><form id="stream-reply-popup-form"><textarea rows="8" cols="30" id="stream-reply-popup-text"></textarea><input type="hidden" name="replyId" value="${id}" /><input type="submit" name="stream-reply-popup-submit" value="送信する" id="stream-reply-popup-submit" /></form></div>
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

<div id="timeline-container">
<div id="timeline-post">
  <div id="timeline-textbox"><textarea rows="8" cols="30" name="timeline-textarea" id="timeline-textarea"></textarea><input type="hidden" name="csrf" id="timeline-csrf" value="" /></div>
  <div id="timeline-submit"><button id="timeline-button">投稿する</button></div>
</div>
  <ul id="streamList">
  </ul>
</div>

<div id="timeline-todo">
◆TODO<br />
・UIを綺麗にする(緑のボタンのデザインとか無駄)<br />
・返信(Reply)、削除をポップアップで行う方法を辞める(回避策がわからないので暫定対処)<br />
・スクリーンネームで表示するようにする<br />
・下までスクロールしたら「続きを読む」みたいなのができるようにする<br />
・PublicFlag(公開範囲)を設定できるようにする。<br />
etc...
</div>
