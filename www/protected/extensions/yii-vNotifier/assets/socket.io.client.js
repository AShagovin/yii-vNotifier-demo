/**
 * @namespace
 */
vn = {};
/**
 * @class
 */
vn.Client = function(clientConfig) {
	// socket io client
	var config = {};
	if(clientConfig.socketioUrl.match(/aws\.af\.cm/)) {
		// API Server hosted on AppFog
		config.transport = ['xhr-polling'];
	}
	var socket = io.connect(clientConfig.socketioUrl + '?secret=' + clientConfig.userSecret);
	
	// handle notify event
	socket.on('notify',function(notification) {
		if(vn.NotificationHandlers[notification.type]) {
			vn.NotificationHandlers[notification.type](notification.message);
		} else {
			vn.NotificationHandlers['__default__'](notification.message);
		}
	});
}

/**
 * Custom Notification handlers
 */
vn.NotificationHandlers = {
	__default__ : function(message) {
		console.log(message);
	}
};


