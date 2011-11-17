(function($){
  $.fn.timelineComment = function(config){
    var default = {
      url: '',
      csrf: '',
    }
    return this.each(function(i){
      $(this).submit(function(){
        var Body = $(this).('#body').val();
        var Csrf = $.ajax

      }
    });
  }
})(jQuery);
