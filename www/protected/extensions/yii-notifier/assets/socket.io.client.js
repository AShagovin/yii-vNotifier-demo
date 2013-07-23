/**
 * @namespace
 */
vn = {};
/**
 * @class
 */
vn.Client = function(clientConfig) {
	this.config = $.extend({}, clientConfig);

	this.initConnection();
};

vn.Client.prototype.initConnection = function() {
	// socket io client
	var self = this;

	this.socket = io.connect(this.config.socketioUrl + '?token=' + this.config.userToken);

	this.socket.on('error', function(message) {
		// ok, the problem here is that all we get is an error message, 
		// so we have 2 options:
		// a) parse the message string, and when it's "handshake unauthorized" then 
		//    we try get a new / correct userToken from our PHP webapp
		// b) on every error we are trying to get new / correct userToken

		// option a)
		if(message === "handshake unauthorized") {
			$.get(self.config.getUserTokenUrl, function(res) {
				self.config.userToken = res.userToken;
				
				delete self.socket;
				delete io.sockets[self.config.socketioUrl];

				self.initConnection();
			}, 'json');
		}

	});

	this.socket.on('connect', function() {
		if(self.config.callback) {
			self.config.callback.apply(self);
		}
	});

	// handle notify event
	this.socket.on('notify',function(notification) {
		if(vn.NotificationHandlers[notification.type]) {
			vn.NotificationHandlers[notification.type](notification.message);
		} else {
			vn.NotificationHandlers.__default__(notification.message);
		}
	});
};

/**
 * Custom Notification handlers
 */
vn.NotificationHandlers = {
	__default__ : function(message) {
		console.log(message);
	}
};


