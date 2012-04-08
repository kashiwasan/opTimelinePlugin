$(function() {
    var elContent = $('#timeline-textarea');
    var elCounter = $('#counter');
    elContent.
        focus(function() {
            (function() {

                var count = elContent.val().length;
                var rest = 140 - count;

                elCounter.text(rest);

                if(rest <= 25 && rest >= 0){
                    elCounter.css({ color: '#FFA500' });
                }
                else if(rest < 0){
                    elCounter.css({ color: '#FF0000' });
                }
                else {
                    elCounter.css({ color: '#000000' });
                }

                var tm = setTimeout(arguments.callee, 100);
                elContent.data('tmCountLetters', tm);
            })();
        }).

        blur(function() {
            var tm = elContent.data('tmCountLetters');
            clearTimeout(tm);
        });
});
