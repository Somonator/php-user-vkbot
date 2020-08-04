function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
	
    location.search.substr(1).split('&').forEach(function (item) {
		tmp = item.split('=');
		if (tmp[0] === parameterName) {
			result = decodeURIComponent(tmp[1]);
		}	
	});
	
    return result;
}

$('.spoiler').each(function() {
	var	s = $(this),
		sh = s.find('.spoiler-header'),
		hh = sh.outerHeight(),
		h_close = hh - 2,
		h_open = hh + s.find('.spoiler-content').outerHeight() + 5;
	
	s.outerHeight(h_close);
		
	sh.click(function () {
		$('.spoiler').not(s).each(function() {
			var sp = $(this).find('.spoiler-header').outerHeight() - 2;
			
			$(this).removeClass('open');
			$(this).animate({'height' : sp}, 200);
		})
			
		if (s.outerHeight() === h_close) {
			var sp = h_open;
			s.addClass('open');
		} else {
			var sp = h_close;
			s.removeClass('open');
		}
		
		s.animate({'height' : sp}, 200);
	});
});

function viewNotice(text, type) {
	var notice = $('.notice'),
		this_notice = notice.clone(),
		icon = type == 'success' ? 'success' : 'error';
		
	this_notice.find('.text').text(text);
	this_notice.find('.icon').addClass(icon);
	
	$('body').append(this_notice);
	
	this_notice.animate({'bottom': 10}, 800);
	
	var del = function() {
		this_notice.animate({'opacity' : 0}, 500, 'linear', function() { 
			this_notice.remove();
		});
	}
	
	setTimeout(del, 10000);
	this_notice.find('.close').click(del);
}