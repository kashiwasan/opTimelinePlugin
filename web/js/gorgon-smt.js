$(function(){
  var timerID;
  timelineAllLoad();
  timerID = setInterval('timelineDifferenceLoad()', 15000);
 
  $('#tosaka_postform_submit').click( function() {
    timelineDifferenceLoad();
  });

  $('#gorgon-loadmore').click( function() {
    $('#timeline-list-loader').show();
    $('#gorgon-loadmore').hide();
    timelineLoadmore();
    $('#timeline-list-loader').hide();
    $('#gorgon-loadmore').show();
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
      success: renderJSON,
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
      success: renderJSON,
      error: function(XMLHttpRequest, textStatus, errorThrown){
      $('#timeline-list-loader').hide();
      $('#timeline-list').text('タイムラインは投稿されていません。');
      $('#timeline-list').show();
      },
    });
  }
}

function renderJSON(json) {
  var textdata = new Array();
  $('.comment-textarea').each(function(){
    var elementId = $(this).attr('data-timeline-id');
    var textValue = $(this).val();
    textdata[elementId] = textValue;
  });
  $('#timeline-list').empty();
  $timelineData = $('#timelineTemplate').tmpl(json.data);
  $('button.timeline-comment-button', $timelineData).timelineComment();
  $('.timeline-now-comment-button', $timelineData).timelineCommentSmtToggle();
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
  if(json.data)
  for(i=0;i<json.data.length;i++)
  {
    if(json.data[i].replies)
    {
      $('#timelineCommentTemplate').tmpl(json.data[i].replies).appendTo('#commentlist-' +json.data[i].id);
    }
    if(!textdata[json.data[i].id])
    {
      textdata[json.data[i].id] = '';
    }
    $('#comment-textarea-' + json.data[i].id).val(textdata[json.data[i].id]);
  }
  $('a[rel^="timelineDelete"]').timelineDelete({callback: "timelineAllLoad()",});
  $('#timeline-list-loader').hide();
  $('#timeline-list').show();
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
  $.getJSON( openpne.apiBase + 'activity/search.json?count=20&since_id=' + lastId, gorgon, function (json) {
    if (json.data[0])
    {
      $('#timeline-list').attr('data-last-id', json.data[0].id);
    }
    $timelineData = $('#timelineTemplate').tmpl(json.data);
    $('button.timeline-comment-button', $timelineData).timelineComment();
    $('.timeline-now-comment-button', $timelineData).timelineCommentSmtToggle();
    $('#timeline-list').prepend($timelineData);
    for(i=0;i<json.data.length;i++)
    {
      if(json.data[i].replies)
      {
        $('#timelineCommentTemplate').tmpl(json.data[i].replies).appendTo('#comment-list-' + json.data[i].id);
      }
    }
  });
}

function timelineLoadmore() {
  var loadmoreId = $('#timeline-list').attr('data-loadmore-id');
  if (gorgon)
  {
    gorgon.apiKey = openpne.apiKey;
  }
  else
  {
    gorgon = {apiKey: openpne.apiKey,}
  }

  $('#timeline-list-loader').css('display', 'show');
  $('#gorgon-loadmore').css('display', 'none');

  $.getJSON( openpne.apiBase + 'activity/search.json?count=20&max_id=' + loadmoreId, gorgon, function (json) {
    $('#timeline-list-loader').css('dispaly', 'show');
    $('#gorgon-loadmore').css('display', 'none');
    var max = json.data.length - 1;
    if (max < 0) 
    {
      max = 0;
    }
    if (json.data[max])
    {
      $('#timeline-list').attr('data-loadmore-id', json.data[max].id);
    }
    $timelineData = $('#timelineTemplate').tmpl(json.data);
    $('button.timeline-comment-button', $timelineData).timelineComment();
    $('.timeline-now-comment-button', $timelineData).timelineCommentSmtToggle();
    $('#timeline-list').after($timelineData);
    for(i=0;i<json.data.length;i++)
    {   
      if(json.data[i].replies)
      {   
        $('#timelineCommentTemplate').tmpl(json.data[i].replies).appendTo('#commentlist-' +json.data[i].id);
      }
    }
    $('#timeline-list-loader').css('display', 'none');
    $('#gorgon-loadmore').css('display', 'show');
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
