function error(msg, target) {
	target.css('display', 'none');
	target.text(msg);
	target.fadeIn(250);
}