$(function(){
  var timerID;
  timelineLoad();
  timerID = setInterval('timelineLoad()', 20000);

    $('input#stream-reply-text').focus(function(){
      clearInterval(timerID);
    });
    $('input#stream-reply-text').blur(function(){
      timerID = setInterval('timelineLoad()', 20000);
    });

  $('#timeline-button').click( function() {
    var Body = $('#timeline-textarea').val();
    var Csrf = $(this).attr('data-post-csrftoken');
    var baseUrl = $(this).attr('data-post-baseurl');
    $.ajax({
      url: baseUrl + '/timeline/post',
      type: 'POST',
      data: 'CSRFtoken=' + Csrf + '&body=' + Body,
      dataType: 'json',
      success: function(data) {
        if(data.status=='success'){
          $('#timeline-textarea').val('');
          timelineLoad();
        }else{
          alert(data.message);
        }
      }
    });
  });

});

function timelineLoad() {
  var baseUrl = window.location.pathname;
  $.getJSON( baseUrl + '/list',function(json){
    $('#streamList').empty();
    $('#timelineTemplate').tmpl(json.data).appendTo('#streamList');
    for(i=0;i<json.data.length;i++){
      if(json.data[i].reply)
      {
        $('#timelineCommentTemplate').tmpl(json.data[i].reply).appendTo('#streamListComment' + json.data[i].id);
      }
    }
    $("a[rel^='prettyPopin']").timelinePopin();
    $("a[rel^='timelineDelete']").timelineDelete();
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
