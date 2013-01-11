$(function(){

  var timerID;
  var timerArray = new Array();
  var timer;
  timelineAllLoad();
  if ( gorgon.timer != undefined )
  {
    timer = gorgon.timer;
  }
  else
  {
    timer = 15000;
  }
  timerID = setInterval('timelineDifferenceLoad()', timer);
  
  $('.basic-mode').remove();
  $('.timeline-mode').show();
 
  $('#timeline_postform_submit').click( function() {
    $('#timeline-submit-loader').show();
    $('#timeline-submit-error').hide();
    $('#photo-file-name').text('');
    $('#timeline_postform_submit').attr('disabled', 'disabled');

    var body = $.trim($('#tosaka_postform_body').val());

    var faceName = $('.face-name').text();
    var faceImg = $('#face').children('.span2').children('img').attr('src');
    var timestamp = new Date(jQuery.now()).toLocaleString();
    var publicFlagText = $('#timeline-public-flag option:selected').val();
    1 == publicFlagText ? publicFlagText = "" : publicFlagText = $('#timeline-public-flag option:selected').text();
    var flashTimelineDom = 
          '<div>'
          + '<div class="timeline-post-member-image">'
            + '<img src="' + faceImg + '" alt="member-image" width="23">'
          + '</div>'
          + '<div class="timeline-post-content">'
            + '<div class="timeline-member-name">'
              + '<a>' + faceName + '</a>'
              + '<div class="timestamp">' + timestamp +'</div>'
            + '</div>'
            + '<div class="timeline-post-body">' + body + '</div>'
            + '<span class="timeline-post-control">'
              + publicFlagText
              + '<img style="float: right; padding-right: 20px;" src="/images/ajax-loader.gif">'
            + '</span>'
          + '</div>'
          + '<div class="timeline-post-control">'
            + '<a class="timeline-comment-link"></a>'
          + '</div>'
        + '</div>';

    if (0 < jQuery.trim(body).length)
    {
      $('#timeline-list').prepend(flashTimelineDom);
    }

    if (gorgon)
    {
      var publicFlag = 1;
      if ('community' != gorgon.target)
      {
        publicFlag = $('#timeline-public-flag option:selected').val()
      }

      var data = {
        body: body,
        target: gorgon.post.foreign,
        target_id: gorgon.post.foreignId,
        apiKey: openpne.apiKey,
        public_flag: publicFlag
      };
    }
    else
    {
      var data = {
        body: body,
        apiKey: openpne.apiKey
      };
    }
    tweetByData(data);
  });

  $('#timeline-upload-photo-button').click(function() {
    $('#timeline-submit-upload').click();
  });

  $('#gorgon-loadmore').click( function() {
    $('#timeline-list-loader').show();
    $('#gorgon-loadmore').hide();
    timelineLoadmore();
    $('#timeline-list-loader').hide();
    $('#gorgon-loadmore').show();
  });

  $('.timeline-comment-loadmore').live('click', function() {
    var timelineId = $(this).attr('data-timeline-id');
    var commentlist = $('#commentlist-' + timelineId);
    var commentLength = commentlist.children('.timeline-post-comment').length;
    $('#timeline-comment-loader-' + timelineId).show();

    $.ajax({
      type: 'GET',
      url: openpne.apiBase + 'activity/commentSearch.json?apiKey=' + openpne.apiKey,
      data: {
        'timeline_id': timelineId,
        'count': commentLength + 20,
      },
      success: function(json){
        commentlist.children('.timeline-post-comment').remove();
        $('#timelineCommentTemplate').tmpl(json.data.reverse()).prependTo('#commentlist-' + timelineId);
        $('#timeline-comment-loader-' + timelineId).hide();

        if (json.data.length < commentLength + 20)
        {
          $('#timeline-comment-loadmore-' + timelineId).hide();
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        $('#commentlist-' + timelineId).hide();
      },  
    }); 
  });
  
  $('#tosaka_postform_body').keyup( function() {
    lengthCheck($(this), $('#timeline_postform_submit'));
  });
  
  $('.timeline-post-comment-form-input').live('keyup', function() {
    lengthCheck($(this), $('button[data-timeline-id=' + $(this).attr('data-timeline-id') + ']'));
  });
});

function timelineAllLoad() {
  if (gorgon)
  {
    gorgon.apiKey = openpne.apiKey;
    $.ajax({
      type: 'GET',
      url: openpne.apiBase + 'activity/search.json',
      data: gorgon,
      success: function (json){
        renderJSON(json, 'all');
        $('#timeline-list-loader').hide();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        $('#timeline-list-loader').hide();
        $('#timeline-list').text('タイムラインは投稿されていません。');
        $('#timeline-list').show();
      },
    });

  }
  else
  {
    $.ajax({
      type: 'GET',
      url: openpne.apiBase + 'activity/search.json?apiKey=' + openpne.apiKey,
      success: function (json){
        renderJSON(json, 'all');
        $('#timeline-list-loader').hide();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        $('#timeline-list-loader').hide();
        $('#timeline-list').text('タイムラインは投稿されていません。');
        $('#timeline-list').show();
      }
    });
  }
}

function timelineDifferenceLoad() {
  var lastId = $('#timeline-list').attr('data-last-id');
  if (gorgon)
  {
    gorgon.apiKey = openpne.apiKey;
  }
  else
  {
    gorgon = {apiKey: openpne.apiKey,}
  }
  //$.getJSON( openpne.apiBase + 'activity/search.json?count=20&since_id=' + lastId, gorgon, function(json){
  $.getJSON( openpne.apiBase + 'activity/search.json?count=20&since_id=' + lastId, gorgon, function(json){
    renderJSON(json, 'diff');
  });
}

function timelineLoadmore() {
  var loadmoreId = $('#timeline-list').attr('data-loadmore-id');
  loadmoreId = loadmoreId - 1;
  if (gorgon)
  {
    gorgon.apiKey = openpne.apiKey;
  }
  else
  {
    gorgon = {apiKey: openpne.apiKey,}
  }
  gorgon.max_id = loadmoreId;

  $.ajax({
    type: 'GET',
    url: openpne.apiBase + 'activity/search.json',
    //url: openpne.apiBase + 'activity/search.json',
    data: gorgon,
    success: function(json){
      renderJSON(json, 'more');
      $('#timeline-loadmore-loading').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown){
      $('#timeline-loadmore-loading').hide();
    },  
  }); 
}

function renderJSON(json, mode) {
  if (undefined == mode)
  {
    mode = 'all';
  }
  if ('all' == mode)
  {
    $('#timeline-list').empty();
  }
  if(json.data && 0 < viewPhoto)
  {
    for(i=0;i<json.data.length;i++)
    {   
      if (!json.data[i].body_html.match(/img.*src=/))
      {   
        if (json.data[i].body.match(/\.(jpg|jpeg|bmg|png|gif)/gi))
        {   
          json.data[i].body_html = json.data[i].body.replace(/((http:|https:)\/\/[\x21-\x26\x28-\x7e]+.(jpg|jpeg|bmg|png|gif))/gi, '<div><a href="$1"><img src="$1"></img></a></div>');
        }   
        else if (json.data[i].body.match(/((http:|https:)\/\/[\x21-\x26\x28-\x7e]+)/gi))
        {   
          json.data[i].body_html = json.data[i].body.replace(/((http:|https:)\/\/[\x21-\x26\x28-\x7e]+)/gi, '<a href="$1"><div class="urlBlock"><img src="http://mozshot.nemui.org/shot?$1"><br />$1</div></a>');
        }   
      }   
    }   
  }


  $timelineData = $('#timelineTemplate').tmpl(json.data);
  $('.timeline-comment-button', $timelineData).timelineComment();
  $('.timeline-comment-link', $timelineData).click(function(){
    $commentBoxArea = $(this).parent().siblings().find('.timeline-post-comment-form');
    $commentBoxArea.show();
    $commentBoxArea.children('.timeline-post-comment-form-input').focus();
  });
  if ('diff' == mode)
  {
    $timelineData.prependTo('#timeline-list');
  }
  else
  {
    $timelineData.appendTo('#timeline-list');
  }
  if ('all' == mode || 'diff' == mode)
  {
    if(json.data[0])
    {
      $('#timeline-list').attr('data-last-id', json.data[0].id);
    }
  }
  if ('all' == mode || 'more' == mode)
  {
    var max = json.data.length - 1;
    if (json.data[max])
    {
      $('#timeline-list').attr('data-loadmore-id', json.data[max].id);
    }
  }
  if(json.data)
  {
    for(i=0;i<json.data.length;i++)
    {
      if(json.data[i].replies)
      {
        $('#timelineCommentTemplate').tmpl(json.data[i].replies.reverse()).prependTo('#commentlist-' +json.data[i].id);
        $('#timeline-post-comment-form-' + json.data[i].id, $timelineData).show();
      }
      if(10 < parseInt(json.data[i].repliesCount))
      {
        $('#timeline-comment-loadmore-' + json.data[i].id).show();
      }
    }
  }
  if ('all' == mode)
  {
    $('#timeline-loading').hide();
  }
  if ('more' == mode)
  {
    $('#timeline-loadmore').show();
    $('#timeline-loadmore-loading').hide();
  }
  $('.timeago').timeago();
}

function convertTag(str) {
  str = str.replace(/&/g,'&amp;');
  str = str.replace(/"/g,'&quot;');
  str = str.replace(/'/g,'&#039;');
  str = str.replace(/</g,'&lt;');
  str = str.replace(/>/g,'&gt;');
  return str;
}

function tweetByData(data)
{
  //reference　http://lagoscript.org/jquery/upload/documentation
  $('#timeline-submit-upload').upload(
    openpne.apiBase + 'activity/post.json', data,
    function (res) {
      returnData = JSON.parse(res);

      if (returnData.status === "error") {

        var errorMessages = {
          file_size: 'ファイルサイズは' + fileMaxSize + 'までです',
          upload: 'アップロードに失敗しました',
          not_image: '画像をアップロードしてください',
          tweet: '投稿に失敗しました'
        };

        var errorType = returnData.type;

        $('#timeline-submit-error').text(errorMessages[errorType]);
        $('#timeline-submit-error').show();

      } else {
        $(".postform").toggle();
        timelineAllLoad();
      }

      $('#timeline-submit-upload').val('');
      $('#tosaka_postform_body').val('');
      $('#timeline-submit-loader').hide();
      $('#counter').text(MAXLENGTH);

    },
    'text' //なぜかJSON形式でうけとることができなかった
    );
}

function lengthCheck(obj, target)
{
  if (0 < $.trim(obj.val()).length && 140 >= obj.val().length)
  {
    target.removeAttr('disabled');
  }
  else
  {
    target.attr('disabled', 'disabled');
  }
}
