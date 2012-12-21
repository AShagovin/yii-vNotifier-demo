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
		// notification are
		notificationArea = document.getElementById('notification-area'),
		// Knockout.JS View Model
		viewModel = {
			notifications : ko.observableArray()
		};

	ko.applyBindings(viewModel,notificationArea);
	
	// handle notify event
	socket.on('notify',function(message) {
		viewModel.notifications.push({message : message});
	});

}
