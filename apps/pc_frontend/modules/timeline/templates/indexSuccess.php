<script id="timeline-list-template" type="text/x-jquery-tmpl">
  <div class="stream-body">
    <div class="stream-image"><img alt="${member.memberName}" alt="${member.memberImage}" height="36" width="36" /></div>
    <div class="stream-membername">${member.memberName}</div>
    <div class="stream-text">${member.body}
      <div id="stream-util"><a href="#" id="timeline-reply" rel="${member.screenName}">Reply</a> | <a href="/op3/timeline/deleteConfirm/${member.activityId}" id="timeline-delete">Delete</a> </div>
    </div>
  </div>
</script>
<script id="timeline-error-temolate" type="text/x-jquery-tmpl">
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
    <div id="timeline-textbox">
      <textarea id="timeline-textbox" name="timeline-textbox">今何してる？</textarea>
    </div>
    <div id="timeline-submit">
      <button name="timeline-submitbutton" id="timeline-submitbutton">共有する</button>
    </div>
  </div>

  <div id="timeline-scroll">
    <div class="stream">
      <div class="stream-header">Home Feed</div>
      <div class="stream-list">

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>
        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>
        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>
        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>
        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>
        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>
        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>
        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>
        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>
        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>
        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>

        <div class="stream-body">
          <div class="stream-image"><img alt="Mr. 柏木" src="/op3/images/no_image.gif" height="36" width="36" /></div>
          <div class="stream-text"><a href="/op3/member/1">kashiwagi</a> うべべべべべべべべっべべべべべべべべべっべべべべべべべ
            <div class="stream-util"><a href="/op3/">Reply</a> | <a href="/op3/">Delete</a></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
