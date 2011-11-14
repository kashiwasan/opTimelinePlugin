$(document).ready(function(){
  $.ajax({
    url: "/op3/timeline/list",
    type: "GET",
    dataType: 'json',
    cache: false,
    async: true,
    success: function(data) {
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
});
