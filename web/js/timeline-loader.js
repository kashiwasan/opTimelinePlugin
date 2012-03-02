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
  // $('button.timeline-comment-button').timelineComment();
  // $('a[rel^="timelineDelete"]').timelineDelete({callback: "timelineAllLoad()",});

  $('#timeline-submit-button').click( function() {
    $('#timeline-submit-loader').show();
    var Body = $('#timeline-textarea').val();
    var Csrf = $(this).attr('data-post-csrftoken');
    var baseUrl = $(this).attr('data-post-baseurl');
    if (gorgon)
    {
      var Data = 'CSRFtoken=' + Csrf + '&body=' + Body + '&foreign=' + gorgon.post.foreign + '&foreignId=' + gorgon.post.foreignId;
    }
    else
    {
      var Data = 'CSRFtoken=' + Csrf + '&body=' + Body;
    }
    $.ajax({
      url: baseUrl + '/timeline/post',
      type: 'POST',
      data: Data,
      dataType: 'json',
      success: function(data) {
        if(data.status=='success'){
          timelineAllLoad();
          $('#timeline-submit-loader').hide();
          $('#timeline-textarea').val('');
        }else{
          $('#timeline-submit-loader').hide();
          alert(data.message);
        }
      }
    });
  });

  $('#timeline-loadmore').click( function() {
    $(this).hide();
    timelineLoadmore();
    $(this).show();
  });
});

function timelineAllLoad() {
  var baseUrl = $('#timeline-submit-button').attr('data-post-baseurl');
  if (gorgon)
  {
    $.getJSON( baseUrl + '/timeline/get', gorgon, renderJSON);
  }
  else
  {
    $.getJSON( baseUrl + '/timeline/get?mode=all&limit=20', renderJSON);
  }
}

function renderJSON(json) {
  var baseUrl = $('#timeline-submit-button').attr('data-post-baseurl');
  var commentCSRF = $('#timeline-submit-button').attr('data-post-csrftoken');
  $('#timeline-list').empty();
  $timelineData = $('#timelineTemplate').tmpl(json.data);
  $('button.timeline-comment-button', $timelineData).timelineComment();
  $('.timeline-comment-link', $timelineData).click(function(){
    $commentBoxArea = $(this).parent().siblings().find('.timeline-post-comment-form');
    $commentBoxArea.show();
    $commentBoxArea.children('.timeline-post-comment-form-input').focus();
  });
  $timelineData.appendTo('#timeline-list');
  if(json.data[0])
  {
    $('#timeline-list').attr('data-last-id', json.data[0].id);
  }
  var max = json.data.length - 1;
  if (json.data[max])
  {
    $('#timeline-list').attr('data-loadmore-id', json.data[max].id);
  }
  if (gorgon.orderBy == "asc")
  {
    if (json.data[0])
    {
      $('#timeline-list').attr('data-loadmore-id', json.data[0].id);
    }
    if (json.data[max])
    {
      $('#timeline-list').attr('data-last-id', json.data[max].id);
    }
  }
  if(json.data)
  for(i=0;i<json.data.length;i++)
  {
    if(json.data[i].reply)
    {
      $('#timelineCommentTemplate').tmpl(json.data[i].reply).prependTo('#commentlist-' +json.data[i].id);
      $('#timeline-post-comment-form-' + json.data[i].id, $timelineData).show();
    }
  }
  $('a[rel^="timelineDelete"]').timelineDelete({callback: "timelineAllLoad()"});
  $('#timeline-loading').hide();
}

function timelineDifferenceLoad() {
  var baseUrl = $('#timeline-list').attr('data-post-baseurl');
  var lastId = $('#timeline-list').attr('data-last-id');
  var commentCSRF = $('#timeline-submit-button').attr('data-post-csrftoken');
  $.getJSON( baseUrl + '/timeline/get?list=check&lastId=' + lastId, gorgon, function (json) {
    if (json.data[0])
    {
      $('#timeline-list').attr('data-last-id', json.data[0].id);
      if (gorgon.orderBy == "asc")
      {
        var max = json.data.length - 1;
        if (json.data[max])
        {
          $('#timeline-list').attr('data-last-id', json.data[max].id);
        }
        $('#timeline-list').scrollTop(750);
      }
      if ( gorgon.notify != undefined )
      {
        if ( gorgon.notify.icon == undefined )
        {
          $('#timeline-desktopify').trigger('notify', [
            json.data[0].memberScreenName + ': ' + json.data[0].body,
            gorgon.notify.title,
          ]);
        }
        else
        {
          $('#timeline-desktopify').trigger('notify', [
            json.data[0].memberScreenName + ': ' + json.data[0].body,
            gorgon.notify.title,
            gorgon.notify.icon,
          ]);
        }
      }
    }
    $timelineData = $('#timelineTemplate').tmpl(json.data);
    $('button.timeline-comment-button', $timelineData).timelineComment();
    $('.timeline-comment-link', $timelineData).click(function(){
      $commentBoxArea = $(this).parent().siblings().find('.timeline-post-comment-form');
      $commentBoxArea.show();
      $commentBoxArea.children('.timeline-post-comment-form-input').focus();
    });
    if (gorgon.orderBy != undefined && gorgon.orderBy == "asc")
    {
      $('#timeline-list').append($timelineData);
    }
    else
    {
      $('#timeline-list').prepend($timelineData);
    }
    for(i=0;i<json.data.length;i++)
    {
      if(json.data[i].reply)
      {
        $('#timelineCommentTemplate').tmpl(json.data[i].reply).prependTo('#commentlist-' +json.data[i].id);
      }
    }
    $('a[rel^="timelineDelete"]').timelineDelete({callback: "timelineAllLoad()"});
  });
}

function timelineLoadmore() {
  var baseUrl = $('#timeline-list').attr('data-post-baseurl');
  var loadmoreId = $('#timeline-list').attr('data-loadmore-id');
  var commentCSRF = $('#timeline-submit-button').attr('data-post-csrftoken');
  $('#timeline-loading').show();
  $.getJSON( baseUrl + '/timeline/get?list=more&moreId=' + loadmoreId, gorgon, function (json) {
    var max = json.data.length - 1;
    if (max < 0) 
    {
      max = 0;
    }
    if (json.data[max])
    {
      $('#timeline-list').attr('data-loadmore-id', json.data[max].id);
    }

    if (gorgon.orderBy == "asc")
    {
      if (json.data[0])
      {
        $('#timeline-list').attr('data-loadmore-id', json.data[0].id);
      }
    }
    $timelineData = $('#timelineTemplate').tmpl(json.data);
    $('button.timeline-comment-button', $timelineData).timelineComment();
    $('.timeline-comment-link', $timelineData).click(function(){
      $commentBoxArea = $(this).parent().siblings().find('.timeline-post-comment-form');
      $commentBoxArea.show();
      $commentBoxArea.children('.timeline-post-comment-form-input').focus();
    });
    if (gorgon.orderBy != undefined && gorgon.orderBy == "asc")
    {
      $('#timeline-list').before($timelineData);
    }
    else
    {
      $('#timeline-list').after($timelineData);
    }
    for(i=0;i<json.data.length;i++)
    {
      if(json.data[i].reply)
      {
        $('#timelineCommentTemplate').tmpl(json.data[i].reply).prependTo('#commentlist-' +json.data[i].id);
      }
    }
    $('a[rel^="timelineDelete"]').timelineDelete({callback: "timelineAllLoad()"});
  }); 
  $('#timeline-loading').hide();
}

function convertTag(str) {
  str = str.replace(/&/g,'&amp;');
  str = str.replace(/"/g,'&quot;');
  str = str.replace(/'/g,'&#039;');
  str = str.replace(/</g,'&lt;');
  str = str.replace(/>/g,'&gt;');
  return str;
}
