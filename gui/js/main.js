$(document).ready(function() {
	if($('#toc').length != 0 ) {
		var selectors = 'h2, h3, h4, h5';
		if($('#main > .row > .col-sm-9 h2').length == 0)
			selectors = 'h3, h4, h5';

		$("#toc").tocify({
			selectors: selectors,
			context: '#main',
			extendPage: false,
			smoothScroll: true,
			scrollTo: 60,
			showEffect: 'fadeIn',
			hideEffect: 'fadeOut'
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

	/* Tooltips */
	$('.socialButtons a, .label').tooltip();
});
