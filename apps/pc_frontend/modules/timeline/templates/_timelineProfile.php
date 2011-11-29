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
  timerArray.push(setInterval('timelineAllLoad()', 30000));
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
    <div class="gorgon-list">
      <div class="gorgon-img"> <img height="48" width="48" src="${memberImage}" alt="${memberScreenName}"> </div>
      <div class="gorgon">
        <div class="gorgon-row">
          <div class="gorgon-text"><a class="gorgon-screenname" href="<?php echo $baseUrl; ?>/member/${memberId}">${memberScreenName}</a> {{html body}} </div>
        </div>
        <div class="gorgon-row"> <div class="_reply-link" style="margin-right: 15px;"><a href="#" class="timestamp"><span class="_timestamp"><img src="<?php echo $baseUrl; ?>/opTimelinePlugin/css/images/clock.png" alt="timelineTimestampIcon" width="15" height="15" /> ${createdAt}</span></a></div> <div class="delteLink" style="display: ${deleteLink};"><a href="#" rel="timelineDelete" class="timestamp" location-url="<?php echo $baseUrl ?>" data-activity-id="${id}" data-activity-body="${convertTag(body)}" data-activity-memberScreenName="${memberScreenName}" data-activity-csrftoken="<?php echo $token; ?>"><img src="<?php echo $baseUrl; ?>/opTimelinePlugin/css/images/trash_can.png" alt="timelineDeleteIcon" width="15" height="15" /> Delete</a></div></div>
      </div>
      <div class="comment-list" id="commentlist-${id}">
      </div>
    </div>
</script>

<script id="timelineCommentTemplate" type="text/x-jquery-tmpl">
        <div class="comment-img"><img src="${memberImage}" width="32" height="32" alt="${memberScreenName}-comment-image"></div>
        <div class="comment">
          <div class="comment-row">
            <div class="comment-text"><a class="comment-screenname">${memberScreenName}</a> {{html body}}</div>
          </div>
          <div class="comment-row"> <div class="_reply-link" style="margin-right: 15px;"><a href="#" class="timestamp"><span class="_timestamp"><img src="<?php echo $baseUrl; ?>/opTimelinePlugin/css/images/clock.png" alt="timelineTimestampIcon" width="15" height="15" /> ${createdAt}</span> - Like!</a></div> <div class="delteLink" style="display: ${deleteLink};"><a href="#" rel="timelineDelete" class="timestamp" location-url="<?php echo $baseUrl ?>" data-activity-id="${id}" data-activity-body="${convertTag(body)}" data-activity-memberScreenName="${memberScreenName}" data-activity-csrftoken="<?php echo $token; ?>"><img src="<?php echo $baseUrl; ?>/opTimelinePlugin/css/images/trash_can.png" alt="timelineDeleteIcon" width="15" height="15" /> Delete</a></div></div>
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

<div class="partsHeading"><h3>メンバーのタイムライン</h3></div>

<div class="main-content" style="min-height: 500px; ">
  <div class="page-header">
    <div id="timeline-list">
    </div>
  </div>
</div>
