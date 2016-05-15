anim = 5000;
var Transition = {
	
	toPage: function(pageTwo, animation) {
		anim = animation;
		dismissPage('top', pageTwo, 500);
	},

	adopt: function() {
		window.onload = function() {
			$('body').addClass('hidden').fadeIn(400);
		}
	}
};


function dismissPage(direction, nextPage, delay=0) {
		var tempObj = {}; tempObj[direction] = '-=2600';
		tempObj['opacity'] = '0';
		$('#pagewrapper').css('position','relative')

		.delay(delay).animate(tempObj,700
		, function(){
			window.location = nextPage;
		});
}