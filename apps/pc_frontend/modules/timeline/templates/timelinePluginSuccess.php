/**********************************************
** jQuery timeilinePopin functions
** how to use : $('#element').timelinePopin();
**              $('#element').timelineDelete();
***********************************************/

(function($){
	var _followScroll = true;
	var _readyBound = false;
	$.fn.timelinePopin = function(settings) {
		settings = jQuery.extend({
			modal : false, /* true/false */
			width : 450, /* false/integer */
			height: 250, /* false/integer */
			opacity: 0.5, /* value from 0 to 1 */
			animationSpeed: 'fast', /* slow/medium/fast/integer */
			followScroll: true, /* true/false */
			loader_path: '', /* path to your loading image */
			callback: function(){} /* callback called when closing the popin */
		}, settings);

		function bindReady(){ // To bind them only once
			if(_readyBound) return;
			_readyBound = true;
			$(window).scroll(function(){ _centerPopin(); });
			$(window).resize(function(){ _centerPopin(); });
		};
		bindReady();
		
		return this.each(function(){
			var popinWidth;
			var popinHeight;
			var $c;

			$(this).click(function(){
				var baseUrl = $(this).attr("location-url");
				settings.loader_path = baseUrl + '/opTimelinePlugin/css/images/prettyPopin/loader.gif';
				buildoverlay();
				buildpopin();
				var id = $(this).attr("data-activity-id"); 
                                var screenName = $(this).attr("data-activity-memberScreenName");
				var foreign = $(this).attr("data-activity-foreign");
				var foreignId = $(this).attr("data-activity-foreign-id");
				var csrfToken = $(this).attr("data-activity-csrftoken");
				// Load the content
					responseText = '<div id="stream-reply-popup-header"><b>' + screenName +'に返信する</b></div><div id="stream-reply-popup-body"><form action="' + baseUrl  + '/timeline/post" id="stream-reply-popup-form"><textarea name="body" rows="8" cols="30" id="stream-reply-popup-text"></textarea><br /><input type="hidden" name="replyId" value="'+id+'" id="stream-reply-replyId" /><input type="hidden" name="foreign" value="' + foreign +'" /><input type="hidden" name="foreignId" value="'+ foreignId  +'" /><input type="hidden" name="CSRFtoken" value="' + csrfToken  +'" /><input type="submit" name="stream-reply-popup-submit" value="送信する" id="stream-reply-popup-submit" /></form></div>';
					$(".prettyPopin .prettyContent .prettyContent-container").html(responseText);
					$(".prettyPopin .prettyContent .prettyContent-container textarea#stream-reply-popup-text").focus();

					// This block of code is used to calculate the width/height of the popin
					popinWidth = settings.width || $('.prettyPopin .prettyContent .prettyContent-container').width() + parseFloat($('.prettyPopin .prettyContent .prettyContent-container').css('padding-left')) + parseFloat($('.prettyPopin .prettyContent .prettyContent-container').css('padding-right'));
					$('.prettyPopin').width(popinWidth);
					popinHeight = settings.height || $('.prettyPopin .prettyContent .prettyContent-container').height() + parseFloat($('.prettyPopin .prettyContent .prettyContent-container').css('padding-top')) + parseFloat($('.prettyPopin .prettyContent .prettyContent-container').css('padding-bottom'));
					$('.prettyPopin').height(popinHeight);
				
					// Now reset the width/height
					$('.prettyPopin').height(45).width(45);
				
					displayPopin();
				return false;
			});

			var displayPopin = function() {
				var scrollPos = _getScroll();

				projectedTop = ($(window).height()/2) + scrollPos['scrollTop'] - (popinHeight/2);
				if(projectedTop < 0) {
					projectedTop = 10;
					_followScroll = false;
				}else{
					_followScroll = settings.followScroll;
				};

				$('.prettyPopin').animate({
					'top': projectedTop,
					'left': ($(window).width()/2) + scrollPos['scrollLeft'] - (popinWidth/2),
					'width' : popinWidth,
					'height' : popinHeight
				},settings.animationSpeed, function(){
					displayContent();
				});
			};
		
			var buildpopin = function() {
				$('body').append('<div class="prettyPopin"><a href="#" id="b_close" rel="close">Close</a><div class="prettyContent"><img src="'+settings.loader_path+'" alt="Loading" class="loader" /><div class="prettyContent-container"></div></div></div>');
				$c = $('.prettyPopin .prettyContent .prettyContent-container'); // The content container
			
				$('.prettyPopin a[rel=close]:eq(0)').click(function(){ closeOverlay(); return false; });
			
				var scrollPos = _getScroll();
			
				// Show the popin
				$('.prettyPopin').width(45).height(45).css({
					'top': ($(window).height()/2) + scrollPos['scrollTop'],
					'left': ($(window).width()/2) + scrollPos['scrollLeft']
				}).hide().fadeIn(settings.animationSpeed);
			};
		
			var buildoverlay = function() {
				$('body').append('<div id="overlay"></div>');
			
				// Set the proper height
				$('#overlay').css('height',$(document).height());
			
				// Fade it in
				$('#overlay').css('opacity',0).fadeTo(settings.animationSpeed,settings.opacity);
			
				if(!settings.modal){
					$('#overlay').click(function(){
						closeOverlay();
					});
				};
			};
		
			var displayContent = function() {
				$c.parent().find('.loader').hide();
				$c.parent().parent().find('#b_close').show();
				$c.fadeIn(function(){
					// Focus on the first form input if there's one
					$(this).find('input[type=text]:first').trigger('focus');

					// Check for paging
					$('.prettyPopin a[rel=internal]').click(function(){
						$link = $(this);

						// Fade out the current content
						$c.fadeOut(function(){
							$c.parent().find('.loader').show();

							// Submit the form
							$.get($link.attr('href'),function(responseText){
								// Replace the content
								$c.html(responseText);

								_refreshContent($c);
							});
						});
						return false;
					});
				

					// Submit the form in ajax
					$('.prettyPopin form').bind('submit',function(){
						$theForm = $(this);
						// Fade out the current content
						$c.fadeOut(function(){
							$c.parent().find('.loader').show();
						
							// Submit the form
							$.post($theForm.attr('action'), $theForm.serialize(),function(responseText){
								// Replace the content
							//	$c.html(responseText);
								closeOverlay();
							//	_refreshContent($c);
							});
						});
						return false;
					});
				});
				$('.prettyPopin a[rel=close]:gt(0)').click(function(){ closeOverlay(); return false; });
			};
	
			var _refreshContent = function(){
				var scrollPos = _getScroll();

				if(!settings.width)	popinWidth = $c.width() + parseFloat($c.css('padding-left')) + parseFloat($c.css('padding-right'));
				if(!settings.height) popinHeight = $c.height() + parseFloat($c.css('padding-top')) + parseFloat($c.css('padding-bottom'));

				projectedTop = ($(window).height()/2) + scrollPos['scrollTop'] - (popinHeight/2);
				if(projectedTop < 0) {
					projectedTop = 10;
					_followScroll = false;
				}else{
					_followScroll = settings.followScroll;
				};

				$('.prettyPopin').animate({
					'top': projectedTop,
					'left': ($(window).width()/2) + scrollPos['scrollLeft'] - (popinWidth/2),
					'width' : popinWidth,
					'height' : popinHeight
				}, settings.animationSpeed,function(){
					displayContent();
				});
			};
		
			var closeOverlay = function() {
				$('#overlay').fadeOut(settings.animationSpeed,function(){ $(this).remove(); });
				$('.prettyPopin').fadeOut(settings.animationSpeed,function(){ $(this).remove(); timelineLoad(); });
			};
		});
	
		function _centerPopin(){
			if(!_followScroll) return;

			// Make sure the popin exist
			if(!$('.prettyPopin')) return;
			
			var scrollPos = _getScroll();

			if($.browser.opera) {
				windowHeight = window.innerHeight;
				windowWidth = window.innerWidth;
			}else{
				windowHeight = $(window).height();
				windowWidth = $(window).width();
			};

			projectedTop = ($(window).height()/2) + scrollPos['scrollTop'] - ($('.prettyPopin').height()/2);
			if(projectedTop < 0) {
				projectedTop = 10;
				_followScroll = false;
			}else{
				_followScroll = true;
			};

			$('.prettyPopin').css({
				'top': projectedTop,
				'left': ($(window).width()/2) + scrollPos['scrollLeft'] - ($('.prettyPopin').width()/2)
			});
		};
	
		function _getScroll(){
			scrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
			scrollLeft = window.pageXOffset || document.documentElement.scrollLeft || 0;
			return {scrollTop:scrollTop,scrollLeft:scrollLeft};
		};
	};

	$.fn.timelineDelete = function(settings) {
		settings = jQuery.extend({
			modal : false, /* true/false */
			width : 450, /* false/integer */
			height: 250, /* false/integer */
			opacity: 0.5, /* value from 0 to 1 */
			animationSpeed: 'fast', /* slow/medium/fast/integer */
			followScroll: true, /* true/false */
			loader_path: '', /* path to your loading image */
			callback: function(){} /* callback called when closing the popin */
		}, settings);

		function bindReady(){ // To bind them only once
			if(_readyBound) return;
			_readyBound = true;
			$(window).scroll(function(){ _centerPopin(); });
			$(window).resize(function(){ _centerPopin(); });
		};
		bindReady();
		
		return this.each(function(){
			var popinWidth;
			var popinHeight;
			var $c;

			$(this).click(function(){
				var baseUrl = $(this).attr("location-url");
				settings.loader_path = baseUrl + '/opTimelinePlugin/css/images/prettyPopin/loader.gif';
				buildoverlay();
				buildpopin();
				var id = $(this).attr("data-activity-id"); 
				var body = $(this).attr("data-activity-body");
				var memberScreenName = $(this).attr("data-activity-memberScreenName");
				var csrfToken = $(this).attr("data-activity-csrftoken");
				// Load the content
					responseText = '<div id="stream-reply-popup-body"><form action="' + baseUrl + '/timeline/delete" id="stream-reply-popup-form">本当にこのタイムラインを削除しますか？<br />'+memberScreenName+': 「'+body+'」<br /><input type="hidden" name="activityId" value="'+id+'" id="stream-reply-replyId" /><input type="hidden" name="CSRFtoken" value="' + csrfToken + '" /><input type="submit" name="stream-reply-popup-submit" value="送信する" id="stream-reply-popup-submit" /></form></div>';
					$(".prettyPopin .prettyContent .prettyContent-container").html(responseText);
					
					// This block of code is used to calculate the width/height of the popin
					popinWidth = settings.width || $('.prettyPopin .prettyContent .prettyContent-container').width() + parseFloat($('.prettyPopin .prettyContent .prettyContent-container').css('padding-left')) + parseFloat($('.prettyPopin .prettyContent .prettyContent-container').css('padding-right'));
					$('.prettyPopin').width(popinWidth);
					popinHeight = settings.height || $('.prettyPopin .prettyContent .prettyContent-container').height() + parseFloat($('.prettyPopin .prettyContent .prettyContent-container').css('padding-top')) + parseFloat($('.prettyPopin .prettyContent .prettyContent-container').css('padding-bottom'));
					$('.prettyPopin').height(popinHeight);
				
					// Now reset the width/height
					$('.prettyPopin').height(45).width(45);
				
					displayPopin();
				return false;
			});

			var displayPopin = function() {
				var scrollPos = _getScroll();

				projectedTop = ($(window).height()/2) + scrollPos['scrollTop'] - (popinHeight/2);
				if(projectedTop < 0) {
					projectedTop = 10;
					_followScroll = false;
				}else{
					_followScroll = settings.followScroll;
				};

				$('.prettyPopin').animate({
					'top': projectedTop,
					'left': ($(window).width()/2) + scrollPos['scrollLeft'] - (popinWidth/2),
					'width' : popinWidth,
					'height' : popinHeight
				},settings.animationSpeed, function(){
					displayContent();
				});
			};
		
			var buildpopin = function() {
				$('body').append('<div class="prettyPopin"><a href="#" id="b_close" rel="close">Close</a><div class="prettyContent"><img src="'+settings.loader_path+'" alt="Loading" class="loader" /><div class="prettyContent-container"></div></div></div>');
				$c = $('.prettyPopin .prettyContent .prettyContent-container'); // The content container
			
				$('.prettyPopin a[rel=close]:eq(0)').click(function(){ closeOverlay(); return false; });
			
				var scrollPos = _getScroll();
			
				// Show the popin
				$('.prettyPopin').width(45).height(45).css({
					'top': ($(window).height()/2) + scrollPos['scrollTop'],
					'left': ($(window).width()/2) + scrollPos['scrollLeft']
				}).hide().fadeIn(settings.animationSpeed);
			};
		
			var buildoverlay = function() {
				$('body').append('<div id="overlay"></div>');
			
				// Set the proper height
				$('#overlay').css('height',$(document).height());
			
				// Fade it in
				$('#overlay').css('opacity',0).fadeTo(settings.animationSpeed,settings.opacity);
			
				if(!settings.modal){
					$('#overlay').click(function(){
						closeOverlay();
					});
				};
			};
		
			var displayContent = function() {
				$c.parent().find('.loader').hide();
				$c.parent().parent().find('#b_close').show();
				$c.fadeIn(function(){
					// Focus on the first form input if there's one
					$(this).find('input[type=text]:first').trigger('focus');

					// Check for paging
					$('.prettyPopin a[rel=internal]').click(function(){
						$link = $(this);

						// Fade out the current content
						$c.fadeOut(function(){
							$c.parent().find('.loader').show();

							// Submit the form
							$.get($link.attr('href'),function(responseText){
								// Replace the content
								$c.html(responseText);

								_refreshContent($c);
							});
						});
						return false;
					});
				

					// Submit the form in ajax
					$('.prettyPopin form').bind('submit',function(){
						$theForm = $(this);
						// Fade out the current content
						$c.fadeOut(function(){
							$c.parent().find('.loader').show();
						
							// Submit the form
							$.post($theForm.attr('action'), $theForm.serialize(),function(responseText){
								// Replace the content
							//	$c.html(responseText);
								closeOverlay();
							//	_refreshContent($c);
							});
						});
						return false;
					});
				});
				$('.prettyPopin a[rel=close]:gt(0)').click(function(){ closeOverlay(); return false; });
			};
	
			var _refreshContent = function(){
				var scrollPos = _getScroll();

				if(!settings.width)	popinWidth = $c.width() + parseFloat($c.css('padding-left')) + parseFloat($c.css('padding-right'));
				if(!settings.height) popinHeight = $c.height() + parseFloat($c.css('padding-top')) + parseFloat($c.css('padding-bottom'));

				projectedTop = ($(window).height()/2) + scrollPos['scrollTop'] - (popinHeight/2);
				if(projectedTop < 0) {
					projectedTop = 10;
					_followScroll = false;
				}else{
					_followScroll = settings.followScroll;
				};

				$('.prettyPopin').animate({
					'top': projectedTop,
					'left': ($(window).width()/2) + scrollPos['scrollLeft'] - (popinWidth/2),
					'width' : popinWidth,
					'height' : popinHeight
				}, settings.animationSpeed,function(){
					displayContent();
				});
			};
		
			var closeOverlay = function() {
				$('#overlay').fadeOut(settings.animationSpeed,function(){ $(this).remove(); });
				$('.prettyPopin').fadeOut(settings.animationSpeed,function(){ $(this).remove(); timelineLoad(); });
			};
		});
	
		function _centerPopin(){
			if(!_followScroll) return;

			// Make sure the popin exist
			if(!$('.prettyPopin')) return;
			
			var scrollPos = _getScroll();

			if($.browser.opera) {
				windowHeight = window.innerHeight;
				windowWidth = window.innerWidth;
			}else{
				windowHeight = $(window).height();
				windowWidth = $(window).width();
			};

			projectedTop = ($(window).height()/2) + scrollPos['scrollTop'] - ($('.prettyPopin').height()/2);
			if(projectedTop < 0) {
				projectedTop = 10;
				_followScroll = false;
			}else{
				_followScroll = true;
			};

			$('.prettyPopin').css({
				'top': projectedTop,
				'left': ($(window).width()/2) + scrollPos['scrollLeft'] - ($('.prettyPopin').width()/2)
			});
		};
	
		function _getScroll(){
			scrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
			scrollLeft = window.pageXOffset || document.documentElement.scrollLeft || 0;
			return {scrollTop:scrollTop,scrollLeft:scrollLeft};
		};
	};
})(jQuery);
