$(function(){
  var timerID;
  var timerArray = new Array();
  var timer;
  var activeClass = 'btn-info';
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

  $("#timeline-upload").change(function(){
    if (document.getElementById("timeline-upload").files[0].name)
    {
      $('#timeline-upload-button').addClass(activeClass);
//      alert(document.getElementById("timeline-upload").files[0].name);
    }
  });

  $('#timeline-submit-button').click( function() {
    $('#timeline-submit-error').text('');
    $('#timeline-submit-error').hide();
    $('#timeline-submit-loader').show();
    var body = $('#timeline-textarea').val();
    if (0 == body)
    {
      $('#timeline-submit-loader').hide();
      $('#timeline-submit-error').text('本文を入力してください');
      $('#timeline-submit-error').show();
      return false;
    }
    if (0 !== document.getElementById('timeline-upload').files.length && !document.getElementById('timeline-upload').files[0].type.match(/^image\/(gif|jpeg|png)$/))
    { 
      $('#timeline-submit-loader').hide();
      $('#timeline-submit-error').text('画像ファイルを選択してください');
      $('#timeline-submit-error').show();
      return false;
    }
    if (gorgon)
    {
      var data = 'body=' + body + '&target=' + gorgon.post.foreign + '&target_id=' + gorgon.post.foreignId + '&apiKey=' + openpne.apiKey;
    }
    else
    {
      var data = 'body=' + body + '&apiKey=' + openpne.apiKey;
    }
    var ajaxOptions = {
      url: openpne.apiBase + 'activity/post.json',
      type: 'POST',
      data: data,
      dataType: 'json',
      success: function(json) {
        timelineAllLoad();
        $('#timeline-submit-loader').hide();
        $('#timeline-textarea').val('');
      },
      error: function(x, r, e){
        $('#timeline-submit-loader').hide();
        $('#timeline-submit-error').text('投稿に失敗しました');
        $('#timeline-submit-error').show();
      },
    };
    if (-1 != window.navigator.userAgent.toLowerCase().indexOf('msie'))
    {
      var xhr = new ActiveXObject("Microsoft.XMLHTTP");
    }
    else
    {
      var xhr = new XMLHttpRequest();
    }
    var upload = xhr.upload,
        activeClass = 'btn-info',
        method = 'POST',
        params = [];
    if (-1 == window.navigator.userAgent.toLowerCase().indexOf('msie'))
    {
      var fd = new FormData();
      $.each(data.split('&'), function() {
        var param = $.map(this.split('='), function(v) {
          return decodeURIComponent(v.replace(/\+/g, ' '));
        });
        fd.append(param[0], param[1]);
      });
      if (0 !== document.getElementById('timeline-upload').files.length)
      {
        $('#timeline-submit-loader').hide();
        $('#timeline-progress').show();
        fd.append('images', document.getElementById('timeline-upload').files[0]);
        upload.addEventListener("progress", function (ev) {
          if (ev.lengthComputable) {
            var pct = Math.round((ev.loaded / ev.total) * 100);
            $('#timeline-progress-bar').css('width', pct + '%');
          }
        }, false);
        upload.addEventListener("load", function () {
          $('#timeline-progress-bar').css('width', '100%');
        }, false);
        upload.addEventListener("error", function (ev) {console.log(ev)}, false);
      }
//      xhr.open(method, openpne.apiBase + 'activity/post.json');
      xhr.open(method, openpne.apiBase + 'activity/post.json');
      xhr.send(fd);
    }
    else
    {
      xhr.open(method, openpne.apiBase + 'activity/post.json', true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.setRequestHeader("Content-length", data.length);
      xhr.setRequestHeader("Connection", "close");
      xhr.send(data);
    }
    xhr.onreadystatechange = function(){ 
      if ( xhr.readyState == 4 ) { 
        if ( xhr.status == 200 ) 
        { 
          timelineAllLoad();
          $('#timeline-submit-loader').hide();
          $('#timeline-progress-bar').css('width', '0%');
          $('#timeline-progress').hide();
          $('#timeline-upload-button').removeClass(activeClass);
          $('#timeline-upload').val('');
          $('#timeline-textarea').val('');
        }
        else
        {
          $('#timeline-submit-loader').hide();
          $('#timeline-progress-bar').css('width', '0%');
          $('#timeline-progress').hide();
          $('#timeline-submit-error').text('投稿に失敗しました');
          $('#timeline-submit-error').show();
        }
      }
    };
//    $('#timeline-upload').upload(
//      openpne.apiBase + 'activity/post.json',
//      data, 
//      function(json) {
//        timelineAllLoad();
//        $('#timeline-submit-loader').hide();
//        $('#timeline-textarea').val('');
//      },
//      'json'
//    );
//    ajaxOptions.data = new FormData($('input[name^="images"]'));
//    $.each($('input[name^="images"]')[0].files, function(i, file)
//    {
//      ajaxOptions.data.append(i, file);
//    });
//    ajaxOptions.data.append('body', body);
//    ajaxOptions.data.append('apiKey', openpne.apiKey);
//    if (gorgon)
//    {
//      ajaxOptions.data.append('target', gorgon.post.foreign);
//      ajaxOptions.data.append('target_id', gorgon.post.foreignId);
//    }
//      $.ajax(ajaxOptions);
  });

  $('#timeline-loadmore').click( function() {
    $('#timeline-loadmore').hide();
    $('#timeline-loadmore-loading').show();
    timelineLoadmore();
  });

  $('.timeline-like-link').live('click', function(){
    var activity_id = $(this).attr('data-activity-id');
    var next_action = $(this).attr('data-next-action');
    var data = { apiKey: openpne.apiKey, activity_id: activity_id };
    $.ajax({
      url: openpne.apiBase + 'like/post.json',
      type: 'post',
      data: data,
      dataType: 'json',
      success: function(json) {
        if ('remove' == next_action)
        {
          $tmplData = $('#timelineLikeAddTemplate').tmpl(json.data, { like_count: json.data.like_count });
          $('#timeline-like-link-' + activity_id).html($tmplData).attr('data-next-action', 'add');
        }
        else
        {
          $tmplData = $('#timelineLikeRemoveTemplate').tmpl(json.data, { like_count: json.data.like_count });
          $('#timeline-like-link-' + activity_id).html($tmplData).attr('data-next-action', 'remove');
        }
        $('#timeline-like-link-' + activity_id).attr('data-already-read', 'false');
      },
      error: function(x, r, e){
        $('#timeline-like-link-' + activity_id).prepend('[error] ');
      },
    });
  });
  $('.timeline-like-link').live('hover', function(){
    var activity_id = $(this).attr('data-activity-id');
    var already_read = $(this).attr('data-already-read');
    var data = { apiKey: openpne.apiKey, activity_id: activity_id };
    if ('true' == already_read)
    {
      return false;
    }
    $.ajax({
      url: openpne.apiBase + 'like/list.json',
      type: 'get',
      data: data,
      dataType: 'json',
      success: function(json) {
        if (undefined != json.data[0])
        {
          $tmplData = $('#timelineLikeListTemplate').tmpl(json.data);
          $('#timeline-like-link-' + activity_id).attr('data-original-title', $tmplData.text() + ' がいいね！と言っています。');
          $('#timeline-like-link-' + activity_id).tooltip({animation: false}).tooltip('destroy').tooltip('show');
        }
        else
        {
          $('#timeline-like-link-' + activity_id).attr('data-original-title', 'いいね！と言っている人はまだいません。');
          $('#timeline-like-link-' + activity_id).tooltip({animation: false}).tooltip('destroy').tooltip('show');
        } 
        $('#timeline-like-link-' + activity_id).attr('data-already-read', 'true');
      },
      error: function(x, r, e){
        $('#timeline-like-link-' + activity_id).attr('data-origin-title', '[error]');
      },
    });
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
  else
  {
    $.ajax({
      type: 'GET',
      url: openpne.apiBase + 'activity/search.json?apiKey=' + openpne.apiKey,
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
    gorgon = {apiKey: openpne.apiKey,}
  }
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
        $('#timelineCommentTemplate').tmpl(json.data[i].replies).prependTo('#commentlist-' +json.data[i].id);
        $('#timeline-post-comment-form-' + json.data[i].id, $timelineData).show();
      }
    }
  }
  $('button.timeline-post-delete-button').timelineDelete();
  $('.timeline-post-image-detail-link').colorbox({
    inline: true,
    width: '610px',
    opacity: '0.8',
    onOpen: function(){ 
      $($(this).attr('href')).show(); 
    },
    onCleanup: function(){ 
      $($(this).attr('href')).hide();
    },
  });
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
  $('.timeline-like-link').tooltip();

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
