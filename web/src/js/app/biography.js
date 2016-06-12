var infos_container = $('.biography_container .infos_container');
var btn = infos_container.find('.more');
btn.on('click', function() {
	infos_container.toggleClass('show_less');
	if($(this).text() == 'show more') {
		$(this).html('show less');
	} else {
		$(this).html('show more');
	}
});