$(function() {
	if (findGetParameter('result') && findGetParameter('notice')) {
		var text = findGetParameter('notice').replace(/\+/g, ' '),
			type = findGetParameter('result');	
		
		viewNotice(text, type);
	}
});

var a = $('.answers'),
	manage,
	current_page = 1;

$(function () {
	$('form.edit').parents('.spoiler').hide();
});

$(function () {
	var manage_placeholder = $('.manage-placeholder');
	
	manage = manage_placeholder.html();
	manage_placeholder.remove();
	
	a.find('tbody tr td:last-child').append(manage);
});

function go_ajax (form, success) {
	$(form).submit(function (e) {
		e.preventDefault();
		
		var form = $(this);
		
		$.ajax({
			type: form.attr('method'),
			url: ajax_url,
			data: form.serialize(),
			success: function (data) {
				if (data) {
					var res = JSON.parse(data);
					
					if (res.result == 'success') {
						success(res);
						form.trigger('reset');
					}
					
					viewNotice(res.notice, res.result);
				} else {
					viewNotice('Данные не пришли.', 'error');
				}
			},
			error: function () {
				viewNotice('Запрос не выполнен. Проблемы с сервером.', 'error');
			}
		});
	});
}

a.delegate('.edit', 'click', function() {
	var form = $('form.edit'),
		spoiler = form.parents('.spoiler'),
		tr = $(this).parents('tr');
	
	form.find('input[name="id"]').val(tr.attr('id'));
	form.find('input[name="question"]').val(tr.find('td:nth-child(1)').text());
	form.find('input[name="answer"]').val(tr.find('td:nth-child(2)').text());
	form.find('input[name="priority"]').val(tr.find('td:nth-child(3)').text().substr(0, 1));
	
	spoiler.show();
	
	$('html, body').animate({
        scrollTop: spoiler.offset().top - 20
    }, 300);	
	
	if (!spoiler.hasClass('open')) {
		spoiler.find('.spoiler-header').trigger('click');
	}
});

go_ajax('.edit', function (res) {
	$('form.edit').parents('.spoiler').hide();

	a.find('tr[id="' + res.data.id + '"] td:nth-child(1)').text(res.data.question);
	a.find('tr[id="' + res.data.id + '"] td:nth-child(2)').text(res.data.answer);
	a.find('tr[id="' + res.data.id + '"] td:nth-child(3)').text(res.data.priority);

	a.find('tr[id="' + res.data.id + '"]').addClass('check');
	
	$('html, body').animate({
        scrollTop: a.find('tr[id="' + res.data.id + '"]').offset().top - 20
	}, 300);
	
	setTimeout(function() {
		a.find('tr[id="' + res.data.id + '"]').removeClass('check');
	}, 800);
});

a.delegate('.delete', 'click', function() {
	var form = $('form.delete'),
		tr = $(this).parents('tr');
	
	form.find('input[name="id"]').val(tr.attr('id'));
	form.trigger('submit');
});

go_ajax('.delete', function (res) {
	a.find('tr[id="' + res.data.id + '"]').remove();
});

$('.reset').click(function(e) {
	e.preventDefault();
	
	if (confirm('Вы действительно хотите стереть все ответы?')) {
		$(this).trigger('submit');
	}
});

$('.pagination').change(function() {
	var page = $(this).find('option:selected').data('page'),
		href = '?page=' + page;
	
	window.location.href = href;
});