$(document).ready(function(){
  timelineLoad();
  setInterval(timelineLoad, 3000);
  $('#timeline-button').click( function() {
    $.ajax({
      url: "/op3/timeline/getCSRFToken",
      dataType: "json",
      cache: false, 
      async: true, 
      success: function(json) {
        var Body = $('#timeline-textarea').val();
        var Csrf = json.token;
        $.ajax({
          url: "/op3/timeline/post",
          type: "POST",
          data: "CSRFtoken="+Csrf+"&body="+Body,
          dataType: 'json',
          success: function(data) {
            if(data.status=="success"){
              $('#timeline-textarea').val('');
              timelineLoad();
            }else{
              alert(data.message);
            }
          }
        });
      }
    });
  });
 

  $('#stream-comment-form-tag').submit( function() { 
    var f = $(this);
    $.ajax({
      url: "/op3/timeline/getCSRFToken",
      dataType: "json",
      cache: false, 
      async: true, 
      success: function(json) {
        var Body = $('#stream-comment-input').val();
        var Csrf = json.token;
        $.ajax({
          url: f.attr('action'),
          type: "POST",
          data: "CSRFtoken="+Csrf+"&body="+Body,
          dataType: 'json',
          success: function(data) {
            if(data.status=="success"){
              $('#stream-comment-input').val('');
              timelineLoad();
            }else{
              alert(data.message);
            }
          }
        });
      }
    });
    return false;
  });

  function timelineLoad() {
    $.ajax({
      url: "/op3/timeline/list",
      type: "GET",
      dataType: 'json',
      cache: false,
      async: true,
      success: function(data) {
        $('#streamList').empty();
        $('#timelineTemplate').tmpl(data.data).appendTo('#streamList');
        for(i=0;i<data.data.length;i++)
        {
          if(data.data[i].reply)
          {
            $('#timelineCommentTemplate').tmpl(data.data[i].reply).appendTo('#streamListComment'+data.data[i].id); 
          }
        }
      }
    });
  }
});
