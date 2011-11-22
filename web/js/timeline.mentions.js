$(document).ready(function(){
  timelineLoad();
  timerID = setInterval('timelineLoad()', 20000);
});

function timelineLoad() {
  var baseUrl = window.location.pathname;
  $.getJSON( baseUrl + '/../listMention',function(json){
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
