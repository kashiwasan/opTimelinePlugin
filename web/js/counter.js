$(function ()
{
    var a = $('#timeline-textarea');
    var b = $('#counter');
    a.focus(function ()
    {
        (function ()
        {
            var d = a.val().length;
            var c = 140 - d;
            b.text(c);
            if (c <= 25 && c >= 0) {
                b.css({
                    color : '#FFA500'
                })
            }
            else if (c < 0) {
                b.css({
                    color : '#FF0000'
                })
            }
            else {
                b.css({
                    color : '#000000'
                })
            }
            var e = setTimeout(arguments.callee, 100);
            a.data('tmCountLetters', e)
        })()
    }).blur(function ()
    {
        var d = a.data('tmCountLetters');
        clearTimeout(d)    })
});
