$(document).ready(function(){
  timelineLoad();
  setInterval(timelineLoad, 3000);

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


  function timelineLoad() {
    $.ajax({
      url: "<?php echo $baseUrl; ?>/timeline/list",
      type: "GET",
      dataType: 'json',
      cache: false,
      async: true,
      success: function(data) {
        $('#streamList').empty();
        $('#timelineCommentJquery').empty();
        $('#timelineTemplate').tmpl(data.data).appendTo('#streamList');
        $("a[rel^='prettyPopin']").timelinePopin();
        $("a[rel^='timelineDelete']").timelineDelete();
        for(i=0;i<data.data.length;i++){
          if(data.data[i].reply)
          {
            $('#timelineCommentTemplate').tmpl(data.data[i].reply).appendTo('#streamListComment'+data.data[i].id); 
            $("a[rel^='timelineDelete']").timelineDelete({callback: timelineLoad(),});
          }
          $("#timelineComment"+data.data[i].id).timelineComment({ url: "<?php echo $baseUrl ?>/timeline/post?replyId="+data.data[i].id, });
          // $("#timeline-reply-link-"+data.data[i].id).timelineReply({ id: data.data[i].id, });
        }
      }
    });
  }

  $('#stream-reply-popup-form').submit(function(){
    var Body = $("#stream-reply-popup-text").val();
    var Csrf = '<?php echo $csrfToken; ?>';
    var ReplyId = $("#stream-reply-replyId").val();
    $c = $('.prettyPopin .prettyContent .prettyContent-container');
    $c.fadeOut(function(){
      $c.parent().find('loader').show();
      $.ajax({
        url: "<?php echo $baseUrl; ?>",
        type: "POST",
        dataType: "json",
        data: "CSRFtoken="+Csrf+"&body="+Body+"&replyId"+ReplyId,
        cache: false,
        async: true,
        success: function(data) {
          if(data.status=="success"){
            $c.parent().find('loader').hide();
            $("stream-reply-popup-body").empty();
            $("stream-reply-popup-body").html("返信に成功しました。");
          }
        },
      });
    });
  });
});
