$(document).ready(function(){
  timelineLoad();
  timerID = setInterval("timelineLoad()", 3000);

  $('#timeline-button').click( function() {
    var Body = $('#timeline-textarea').val();
    var Csrf = "<?php echo $csrfToken; ?>";
    $.ajax({
      url: "<?php echo $baseUrl; ?>/timeline/post",
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
  });
});



function timelineLoad() {
  $.getJSON("<?php echo $baseUrl; ?>/timeline/list",function(json){
    $("#streamList").empty();
    $("#timelineTemplate").tmpl(json.data).appendTo("#streamList");
    $("a[rel^='prettyPopin']").timelinePopin();
    $("a[rel^='timelineDelete']").timelineDelete();
    for(i=0;i<json.data.length;i++){
      if(json.data[i].reply)
      {
        $("#timelineCommentTemplate").tmpl(json.data[i].reply).appendTo("#streamListComment"+json.data[i].id);
      }
    }
  });
}
