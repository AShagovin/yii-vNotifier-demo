/**
 * @namespace
 */
vNotifier = {};
/**
 * @class
 */
vNotifier.Client = function(config) {
		// socket io client
	var socket = io.connect(config.socketioUrl + '?secret=' + config.userSecret),
		// notification area
		notificationArea = document.getElementById('notification-area'),
		// KnockoutJS View Model
		viewModel = {
			notifications : ko.observableArray()
		};

	ko.applyBindings(viewModel,notificationArea);
	
	// handle notify event
	socket.on('notify',function(message) {
		viewModel.notifications.push({message : message});
		// auto remove first message after 5sec
		setTimeout(function() {
			viewModel.notifications.shift();
		}, 5000);
	});

}
