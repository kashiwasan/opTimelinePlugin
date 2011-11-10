$(function(){
  $('#timeline-submitbutton').click(function(){
    body = $('#timeline-textbox').val();
    $.ajax({
      type: "POST",
      url: "",
      dataType: "json",
      data: { "body" : body},
      success: function(json){
        if(json.status="success")
        {
          $('#timeline-list-template').tmpl(json.data).appendTo('#timeline-json-list');
        }
      }
    })
  });
});
