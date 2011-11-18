$(document).ready(function(){
  timelineLoad();
  timerID = setInterval("timelineLoad()", 3000);

  $('#timeline-button').click( function() {
    var Body = $('#timeline-textarea').val();
    var Csrf = "<?php echo $csrfToken; ?>";
    $.ajax({
      url: "<?php echo $baseUrl; ?>/timeline/post",
      type: "POST",
      data: "foreign=<?php echo $foreigntable; ?>&foreignId=<?php echo $cid; ?>&CSRFtoken="+Csrf+"&body="+Body,
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
  $.getJSON("<?php echo $baseUrl; ?>/timeline/list?m=<?php echo $mode; ?>&cid=<?php echo $cid; ?>",function(json){
    $("#streamList").empty();
    $("#timelineTemplate").tmpl(json.data).appendTo("#streamList");
    for(i=0;i<json.data.length;i++){
      if(json.data[i].reply)
      {
        $("#timelineCommentTemplate").tmpl(json.data[i].reply).appendTo("#streamListComment"+json.data[i].id);
      }
    }
    $("a[rel^='prettyPopin']").timelinePopin();
    $("a[rel^='timelineDelete']").timelineDelete();
  });
}

function convertTag(str) {
  str = str.replace(/&/g,"&amp;");
  str = str.replace(/"/g,"&quot;");
  str = str.replace(/'/g,"&#039;");
  str = str.replace(/</g,"&lt;");
  str = str.replace(/>/g,"&gt;");
  return str;
}
