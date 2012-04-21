/*
 *  OpenPNE - opTimelinePlugin Character Count Script 1.00
 *  Createed by Yudai Sonoda
 *  http://net-top.jp/
 *
 *  The author is completely abandoned the right of this script.
 *  (It is also possible diversion to other projects)
 *
 *  Built for jQuery library
 *  http://jquery.com/
 *  and opTimelinePlugin
 *  https://github.com/kashiwasan/
 *
 */

$(function ()
  {

    // Configuration Properties
    var allowed = 140;
    var warning = 25;
    var textarea = $('#timeline-textarea');
    var counter = $('#counter');

    counter.text(allowed);
    textarea.keyup(function ()
      {
         count = (allowed - $(this).val().length);
         counter.text(count);
         if (count <= warning && count >= 0) {
           counter.css({
             color : '#FFA500'
           })
         }
         else if (count < 0) {
           counter.css({
             color : '#FF0000'
           })
         }
         else {
           counter.css({
             color : '#000000'
           })
         }
     });
});
