var isUploadSuccess = 'hoge';

$(function(){
  var timerID;
  var timerArray = new Array();
  var timer;

  gorgon.image_size = 'large';

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
    $(this).attr('disabled', 'disabled');
    $('#timeline-submit-error').text('');
    $('#timeline-submit-error').hide();
    $('#timeline-submit-loader').show();
    $('#photo-file-name').text('');
    $('#photo-remove').hide();

    var body = $('#timeline-textarea').val();

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
      url: openpne.apiBase + 'activity/commentSearch.json?apiKey=' + openpne.apiKey,
      data: {
        'timeline_id': timelineId,
        'count': commentLength + 20
      },
      success: function(json){
        commentlist.children().remove();
        $('#timelineCommentTemplate').tmpl(json.data.reverse()).prependTo(commentlist);
        $('#timeline-comment-loader-' + timelineId).hide();

        if (json.data.length < commentLength + 20)
        {
          $('#timeline-comment-loadmore-' + timelineId).hide();
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        $(commentlist).hide();
      }  
    }); 
  });

  $('#timeline-submit-upload').change(function() {
    var fileName = $('#timeline-submit-upload').val();
    $('#photo-remove').show();
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

  $('#timeline-submit-upload').change(function() {

      $('#timeline-submit-error').hide();
      $('#timeline-submit-error').text('');

      var size = this.files[0].size;

      if (size >= fileMaxSizeInfo['size']) {
        $('#timeline-submit-error').show();

        var errorMessage = 'ファイルは' + fileMaxSizeInfo['format'] + '以上はアップロードできません';
        $('#timeline-submit-error').text(errorMessage);

        $('#timeline-submit-upload').val('');
        $('#photo-file-name').text('');

      }

  });

  $('#timeline-textarea').keyup( function() {
    lengthCheck($(this), $('#timeline-submit-button'));
  });

  $('.timeline-post-comment-form-input').live('keyup', function() {
    lengthCheck($(this), $('button[data-timeline-id=' + $(this).attr('data-timeline-id') + ']'));
  });
  
  $('#photo-remove').click( function() {
    $('#timeline-submit-upload').val('');
    $('#photo-file-name').text('');
    $(this).hide();
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
      success: function(response){

        if ($.isEmptyObject(response.data))
        {
          $('#timeline-loading').hide();
          $('#timeline-list').text('タイムラインは投稿されていません。');
          $('#timeline-list').show();
        }
        else
        {
          renderJSON(response, 'all');
        }
        
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
      url: openpne.apiBase + 'activity/search.json?apiKey=' + openpne.apiKey,
      success: function(response){

        if ($.isEmptyObject(response.data))
        {
          $('#timeline-loading').hide();
          $('#timeline-list').text('タイムラインは投稿されていません。');
          $('#timeline-list').show();
        }
        else
        {
          renderJSON(response, 'all');
        }

      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        $('#timeline-loading').hide();
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
    gorgon = {
      apiKey: openpne.apiKey
    }
  }
  $.getJSON( openpne.apiBase + 'activity/search.json?count=20&since_id=' + lastId, gorgon, function(json){
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
      apiKey: openpne.apiKey
    }
  }
  gorgon.max_id = loadmoreId;

  $.ajax({
    type: 'GET',
    url: openpne.apiBase + 'activity/search.json',
    data: gorgon,
    success: function(json){
      renderJSON(json, 'more');
    },
    error: function(XMLHttpRequest, textStatus, errorThrown){
      $('#timeline-loadmore-loading').hide();
    }  
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
    autoLinker(json);
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
      if(10 < parseInt(json.data[i].replies_count))
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
    }
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
      var resCheck = responceCheck(res);
      if (false !== resCheck)
      {
        $('#timeline-submit-error').text(resCheck);
        $('#timeline-submit-error').show();
        $('#timeline-submit-loader').hide();
        return;
      }

      //jquery.uploadだとbrタグがなぜか<br ="">みたいな感じでレスポンスが戻ってきた API自体は問題ないが
      res = res.replace(/<br \\=\"\">/g,  '<br />');
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
        if ($.browser.msie && $.browser.version > 6)
        {
        }
        $('#timeline-submit-error').show();
        $('#timeline-submit-loader').hide();

      } else {
        $('#timeline-submit-error').text('');
        timelineAllLoad();
      }

      $('#timeline-submit-upload').val('');
      $('#timeline-submit-upload').text('');
      $('#timeline-textarea').val('');
      $('#timeline-submit-loader').hide();
      $('#counter').text(MAXLENGTH);

    },
    'text' //なぜかJSON形式でうけとることができなかった
    );
}

function autoLinker(json)
{
  for(i=0;i<json.data.length;i++)
  {
    if (!json.data[i].body_html.match(/img.*src=/))
    {
      if (json.data[i].body.match(/\.(jpg|jpeg|png|gif)/))
      {
        json.data[i].body_html = json.data[i].body.replace(/((http:|https:)\/\/[\x21-\x26\x28-\x7e]+.(jpg|jpeg|png|gif))/gi, '<div><a href="$1"><img src="$1"></img></a></div>');
      }
      else if (json.data[i].body.match(/((http:|https:)\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_\-]+))/))
      {
        var youtubeId = json.data[i].body.substring(json.data[i].body.lastIndexOf('v=') + 2, json.data[i].body.length);
        var iframe = '<iframe width="370" height="277" src="http://www.youtube.com/embed/' + youtubeId + '" frameborder="0" allowfullscreen></iframe>';
        json.data[i].body_html = json.data[i].body.replace(/((http:|https:)\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_\-]+))/gi, '<div>' + iframe + '</div>');
      }
      else if (json.data[i].body.match(/((http:|https:)\/\/www\.amazon\..*\/([a-zA-Z0-9_\-]+)\/.*)/))
      {
        var match_id = json.data[i].body.match(/(?:ASIN|product|dp)\/([^\/]+)/i);
        if (match_id) {
          id = RegExp.$1;
        }
        var url = 'http://amazon.openpne.jp/?id=' + id + '&tag=';
        json.data[i].body_html = json.data[i].body.replace(/((http:|https:)\/\/www\.amazon\..*\/([a-zA-Z0-9_\-]+)\/.*)/, '<div><iframe MARGINWIDTH="0" MARGINHEIGHT="0" HSPACE="0" VSPACE="0" FRAMEBORDER="0" SCROLLING="no" BORDERCOLOR="#000000" src="' + url + '" name="sample" width="360" height="320">この部分はインラインフレームを使用しています。</iframe></div>');
      }
      else if (json.data[i].body.match(/((http:|https:)\/\/[\x21-\x26\x28-\x7e]+)/))
      {
        json.data[i].body_html = json.data[i].body.replace(/((http:|https:)\/\/[\x21-\x26\x28-\x7e]+)/gi, '<a href="$1"><div class="urlBlock"><img src="http://mozshot.nemui.org/shot?$1"><br />$1</div></a>');
      }
    }
    json.data[i].body_html = json.data[i].body_html.replace(/&lt;br \/&gt;/g, '<br />');
  }
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

function responceCheck(res)
{
  if (0 <= res.indexOf('\<pre'))
  {
    return 'エラーが発生しました。再度読み込んで下さい。';
  }
  return false;
}
