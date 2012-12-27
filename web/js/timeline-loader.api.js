var isUploadSuccess = 'hoge';

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
  if ( gorgon.notify !== undefined )
  {
    $('#timeline-desktopify').desktopify({
      unsupported : function(){
        $('#timeline-desktopify').hide();
      }
    }).trigger('click');
  }

  $('#timeline-submit-button').click( function() {


    $('#timeline-submit-error').text('');
    $('#timeline-submit-error').hide();
    $('#timeline-submit-loader').show();
    $('#photo-file-name').text('');

    var body = $('#timeline-textarea').val();

    if (gorgon)
    {
      var data = {
        body: body,
        target: gorgon.post.foreign,
        target_id: gorgon.post.foreignId,
        apiKey: openpne.apiKey,
        public_flag: $('#timeline-public-flag option:selected').val()
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

  $('#timeline-loadmore').click( function() {
    $('#timeline-loadmore').hide();
    $('#timeline-loadmore-loading').show();
    timelineLoadmore();
  });

  $('.timeline-comment-loadmore').live('click', function() {
    var timelineId = $(this).attr('data-timeline-id');
    var commentlist = $('#commentlist-' + timelineId);
    var commentLength = commentlist.children('.timeline-post-comment').length;
    $('#timeline-comment-loader-' + timelineId).show();

    $.ajax({
      type: 'GET',
      url: openpne.apiBase + 'timeline/commentSearch.json?apiKey=' + openpne.apiKey,
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

  $('#timeline-submit-upload').change(function() {
    var fileName = $('#timeline-submit-upload').val();
    if (20 > fileName.length)
    {
      $('#photo-file-name').text(fileName);
    }
    else
    {
      $('#photo-file-name').text(fileName.substring(0, 19) + '…');
    }
  });

  $('#timeline-upload-photo-button').click(function() {
    $('#timeline-submit-upload').click();
  });
});

function timelineAllLoad() {
  if (gorgon)
  {
    gorgon.apiKey = openpne.apiKey;
    $.ajax({
      type: 'GET',
      url: openpne.apiBase + 'timeline/search.json',
      data: gorgon,
      success: function(json){
        renderJSON(json, 'all');
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        $('#timeline-loading').hide();
        $('#timeline-list').text('タイムラインは投稿されていません。');
        $('#timeline-list').show();
      }  
    }); 
  }
  else
  {
    $.ajax({
      type: 'GET',
      url: openpne.apiBase + 'timeline/search.json?apiKey=' + openpne.apiKey,
      success: function(json){
        renderJSON(json, 'all');
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        $('#timeline-loading').hide();
        $('#timeline-list').text('タイムラインは投稿されていません。');
        $('#timeline-list').show();
      },  
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
    gorgon = {
      apiKey: openpne.apiKey,
    }
  }
  $.getJSON( openpne.apiBase + 'timeline/search.json?count=20&since_id=' + lastId, gorgon, function(json){
    if (json.data)
    {
      renderJSON(json, 'diff');
    }
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
    gorgon = {
      apiKey: openpne.apiKey,
    }
  }
  gorgon.max_id = loadmoreId;

  $.ajax({
    type: 'GET',
    url: openpne.apiBase + 'timeline/search.json',
    data: gorgon,
    success: function(json){
      renderJSON(json, 'more');
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
  $('button.timeline-post-delete-button').timelineDelete();
  $('.timeline-post-delete-confirm-link').colorbox({
    inline: true,
    width: '610px',
    opacity: '0.8',
    onOpen: function(){ 
      $($(this).attr('href')).show(); 
    },
    onCleanup: function(){ 
      $($(this).attr('href')).hide();
    },
    onClosed: function(){
      timelineAllLoad();
    },
  });
  if ('all' == mode)
  {
    $('#timeline-loading').hide();
  }
  if ('more' == mode)
  {
    $('#timeline-loadmore').show();
    $('#timeline-loadmore-loading').hide();
  }
}

function convertTag(str) {
  str = str.replace(/&/g,'&amp;');
  str = str.replace(/"/g,'&quot;');
  str = str.replace(/'/g,'&#039;');
  str = str.replace(/</g,'&lt;');
  str = str.replace(/>/g,'&gt;');
  return str;
}

function lengthCheck(obj)
{
  if (MAXLENGTH < obj.value.length)
  {
    $('#timeline-submit-button').attr('disabled','disabled');
  }
  else if ($('#timeline-submit-button').attr('disabled'))
  {
    $('#timeline-submit-button').removeAttr('disabled');
  }
}

function tweetByData(data)
{
  //reference　http://lagoscript.org/jquery/upload/documentation
  $('#timeline-submit-upload').upload(
    openpne.apiBase + 'timeline/post.json', data,
    function (res) {

      res = res.match(/\{.*\}/);

      returnData = JSON.parse(res);

      if (returnData.status === "error") {

        var errorMessages = {
          file_size: 'ファイルサイズが容量オーバーです',
          upload: 'アップロードに失敗しました',
          not_image: '画像をアップロードしてください',
          tweet: '投稿に失敗しました'
        };

        var errorType = returnData.type;

        $('#timeline-submit-error').text(errorMessages[errorType]);
        $('#timeline-submit-error').show();

      } else {
        $('#timeline-submit-error').text('');
        timelineAllLoad();
      }

      $('#timeline-submit-upload').val('');
      $('#timeline-textarea').val('');
      $('#timeline-submit-loader').hide();
      $('#timeline-textarea').val('');
      $('#counter').text(MAXLENGTH);

    },
    'text' //なぜかJSON形式でうけとることができなかった
    );
}
