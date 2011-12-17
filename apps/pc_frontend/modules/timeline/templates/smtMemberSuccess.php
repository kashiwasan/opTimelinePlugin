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


<script type="text/javascript">
$(function(){
  var timerID;
  var timerArray = new Array();
  timelineAllLoad();
  timerID = setInterval('timelineDifference()', 30000);
});

function timelineAllLoad() {
  var baseUrl = '<?php echo $baseUrl; ?>';
  $.getJSON( baseUrl + '/timeline/listMember?id=<?php echo $memberId; ?>', renderJSON);
}

function renderJSON(json) {
  var textdata = new Array();
  $('.comment-textarea').each(function(){
    var elementId = $(this).attr('data-timeline-id');
    var textValue = $(this).val();
    textdata[elementId] = textValue;
  });
  var baseUrl = '<?php echo $baseUrl; ?>';
  var commentCSRF = '<?php echo $token; ?>';
  $('#timeline-list').empty();
  $('#timelineTemplate').tmpl(json.data).appendTo('#timeline-list');
  for(i=0;i<json.data.length;i++)
  {
    if(json.data[i].reply)
    {
      $('#timelineCommentTemplate').tmpl(json.data[i].reply).appendTo('#commentlist-' +json.data[i].id);
    }
    if(!textdata[json.data[i].id])
    {
      textdata[json.data[i].id] = '';
    }
    $('#commentlist-'+json.data[i].id).append('<form><textarea data-timeline-id="' + json.data[i].id  + '" data-post-csrftoken="' + commentCSRF + '" class="comment-textarea" id="comment-textarea-' + json.data[i].id  + '"></textarea><button data-timeline-id="' + json.data[i].id  + '" data-post-csrftoken="' + commentCSRF + '" data-post-baseurl="' + baseUrl + '" class="comment-button button">投稿</button></form>');
    $('#comment-textarea-' + json.data[i].id).val(textdata[json.data[i].id]);
  }
  $('button.comment-button').timelineComment();
  $('a[rel^="timelineDelete"]').timelineDelete({callback: "timelineAllLoad()",});
}

function timelineDifferenceLoad() {
  var baseUrl = $('#gorgon').attr('data-post-baseurl');
  var lastId = $('#gorgon').attr('data-last-id');
  $.getJSON( baseUrl + '/timeline/listDifference?id=' + lastId, function (json) {
    $('#timelineTemplate').tmpl(json.data).before('#timeline-list');
    for(i=0;i<json.data.length;$i++)
    {   
      if(json.data[i].reply)
      {   
        $('#timelineCommentTemplate').tmpl(json.data[i].reply).appendTo('#comment-list-' + json.data[i].id);
      }   
      $('#commentlist-'+json.data[i].id).append('<form><textarea data-timeline-id="' + json.data[i].id  + '" data-post-csrftoken="' + commentCSRF + '" class="comment-textarea" id="comment-textarea-' + json.data[i].id  + '"></textarea><button data-timeline-id="' + json.data[i].id  + '" data-post-csrftoken="' + commentCSRF + '" data-post-baseurl="' + baseUrl + '" class="comment-button button">投稿</button></form>');
    $('button.comment-button').timelineComment();
    $('a[rel^="timelineDelete"]').timelineDelete({callback: "timelineAllLoad()"});
    }   
  });
}


function convertTag(str) {
  str = str.replace(/&/g,'&amp;');
  str = str.replace(/"/g,'&quot;');
  str = str.replace(/'/g,'&#039;');
  str = str.replace(/</g,'&lt;');
  str = str.replace(/>/g,'&gt;');
  return str;
}

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
