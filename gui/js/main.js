$(document).ready(function() {

	// copy text
	/*$('.package h3').each(function(){
		$jcopyimg = $('<img class="jcopy" src="ui/img/jcopy.png" />');
		$(this).append($jcopyimg);
		$(this).find('.jcopy').zclip({
			path:'ui/inc/ZeroClipboard.swf',
			copy:$(this).text()
		});
	});*/

	/* Smoothscroll
	****************/
	$('a[href*=#]').click(function() {
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
		   var $target = $(this.hash);
		   $target = $target.length && $target || $('[name=' + this.hash.slice(1) +']');
		   if ($target.length) {
		       var targetOffset = $target.offset().top;
		       $('html,body').animate({scrollTop: targetOffset-60}, 500);
		       return false;
		   }
		}
	});

	/* TwitterBar
	*******************/
    /*
	$.tweetBarSize = function() {
        var height_li = 30;
	    $(".tweets-slide ul li").each(function() {
	        $(this).css('height', '');
	        if ($(this).outerHeight(true) > height_li) height_li = $(this).outerHeight(true);
	    });
	    $(".tweets-slide ul li").each(function() {
	        var margin = Math.floor((height_li-$(this).outerHeight(true))/2);
	        $(this).css('height', height_li);
	        $(this).children("p").css('margin-top', margin);
	    });
	};

	$.ajax({
        url: 'http://api.twitter.com/1/statuses/user_timeline.json/',
        type: 'GET',
        dataType: 'jsonp',
        data: {
            screen_name: 'phpfatfree',
            include_rts: true,
            count: 6,
            include_entities: true
        },
        success: function(data, textStatus, xhr) {
            var html = '';
            for(var i=0, max=data.length; i<max; i++) {
            	html+='<li>'+data[i].text+'</li>';
            }
            $(".tweets-slide ul").append($(html));
            $.tweetBarSize();
            $('.tweets-slide').flexslider({
                animation: "slide",
                keyboard: false,
                controlNav: false,
                direction: "vertical",
                pauseOnHover: true,
                animationSpeed: 400,
                slideshowSpeed: 5000,
                controlsContainer: "#tweetnav"
            });
            $(window).on('resize', $.tweetBarSize);
        }
    });
    */

    /* Table of Contents
    **********************/
    if($('#toc').length != 0 ) {
        var selectors = 'h2, h3, h4, h5';
        if($('#main > .row > .span9 h2').length == 0)
            selectors = 'h3, h4, h5';
        $("#toc").tocify({
            selectors: selectors,
            context: '#main',
            extendPage:false,
            hashGenerator:'pretty',
            scrollTo:100
        }).data("tocify");

        $("#toc").parents('.row').eq(0).css({position:'relative'});
        $window = $(window);

        $.tocAffix = function(){
            var mainBottom = $('#main').offset().top + $('#main').height();
            if((mainBottom - $window.height() - ($('#toc').height() - $window.height()) - 90) < $window.scrollTop()) {
                $('#toc').toggleClass('bottom',true);
            } else {
                $('#toc').toggleClass('bottom', false);
            }
        }
        $(window).on('scroll', $.tocAffix);
    }

    $('.social-btn, .label').tooltip();

});
