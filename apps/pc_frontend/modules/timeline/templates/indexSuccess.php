<script id="timelineTemplate" type="text/x-jquery-tmpl">
  <li class="stream-body">
    <div class="stream-image"><a href="<?php echo $baseUrl; ?>/member/${memberId}" title="${memberScreenName}"><img src="${memberImage}" alt="${memberScreenName}" width="36" height="36" /></a></div>
    <div class="stream-right">
      <div class="stream-membername"><a href="<?php echo $baseUrl; ?>/member/${memberId}" title="${memberScreenName}">${memberScreenName}</a><span class="small">(${memberName})</small></div>
      <div class="stream-text">{{html body}}
      <div class="stream-util"><span class="small"><a href="#" location-url="<?php echo $baseUrl; ?>" data-activity-id="${id}" data-activity-memberScreenName="${memberScreenName}" data-activity-csrftoken="<?php echo $token; ?>" rel="prettyPopin" class="timelineReplyLink">コメントする</a> | <span id="timeline-delete-link-${id}" style="display: ${deleteLink};"><a location-url="<?php echo $baseUrl ?>" data-activity-id="${id}" data-activity-body="${convertTag(body)}" data-activity-memberScreenName="${memberScreenName}" data-activity-csrftoken="<?php echo $token; ?>" rel="timelineDelete" href="#" id="delete-timeline">削除する</a></span> (${createdAt})</span></div>
      <ul id="streamListComment${id}" class="commentList"></ul>
      <div id="stream-comment"><input type="text" name="body" value="" id="stream-reply-text" /></div>
      </div>
    </div>
  </li>
</script>

<script id="timelineCommentTemplate" type="text/x-jquery-tmpl">
  <li class="stream-comment-body">
    <div class="stream-membername"><a href="<?php echo $baseUrl; ?>/member/${memberId}" title="${memberScreenName}">${memberScreenName}</a><span class="small">(${memberName})</span></div>
    <div class="stream-text">{{html body}}
      <div class="stream-util"><span class="small"><span id="timeline-delete-link-${id}" style="display: ${deleteLink};"><a rel="timelineDelete" href="#" location-url="<?php echo $baseUrl; ?>" data-activity-id="${id}" data-activity-body="${convertTag(body)}" data-activity-memberScreenName="${memberScreenName}" data-activity-csrftoken="<?php echo $token; ?>" id="detete-timeline">削除する</a></span> (${createdAt})</span></div>
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

<a href="<?php echo $baseUrl; ?>/member/config?category=timelineScreenName">■スクリーンネーム設定画面</a><br />

<div id="timeline-container">
<div id="timeline-post">
  <div id="timeline-textbox"><textarea rows="8" cols="30" name="timeline-textarea" id="timeline-textarea"></textarea><input type="hidden" name="csrf" id="timeline-csrf" value="" /></div>
  <div id="timeline-submit"><button id="timeline-button" data-post-csrftoken="<?php echo $token; ?>" data-post-baseurl="<?php echo $baseUrl; ?>">投稿する</button></div>
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
