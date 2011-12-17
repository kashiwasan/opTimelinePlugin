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
<div class="timeline font10 row" id="commentlist-${id}">
  <div class="span2"><a href="<?php echo url_for('@homepage'); ?>"><img src="${memberImage}" class="rad6" width="46" height="46"> </a></div>
  <div class="span10">
    {{html body}}
  </div>
</div>

</script>

<script id="timelineCommentTemplate" type="text/x-jquery-tmpl">
<div class="comment font10 row">
  <div class="span10 offset2">
    <div class="row">
      <hr class="toumei2">
      <div class="span1"><img src="${memberImage}" class="rad6" width="23" height="23"> </div>
      <div class="span9">{{html body}}</div>
    </div>
  </div>
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

<div class="row">
  <div class="gadget_header span12">Timeline</div>
</div>

<a href="<?php echo $baseUrl; ?>/member/config?category=timelineScreenName">■スクリーンネーム設定画面</a><br />

<div id="timeline-list">

</div>
<div id="gorgon-submit" data-post-baseurl="<?php echo $baseUrl ?>"></div>

