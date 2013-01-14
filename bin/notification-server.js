var config = require('./conf.js'),
	redis = require('redis'),
	// TODO: need to able to configure redis client vie the conf.js
	redisClient = redis.createClient(),
	fs = require('fs'),
	io = require('socket.io').listen(config.io.port),
	sockets = {};

io.configure(function() {
	// set authorization
	io.set('authorization',function(handshakeData,callback) {
		if(handshakeData.query.secret) {
			// when the user's secret is in redis then we trust him as an authenticated user
			// TODO: check secret in redis
			if(redisClient.get(handshakeData.query.secret)) {
				callback(null,true);
			} else {
				// unauthenticated user
				callback(null,false);
			}
		} else {
			// no secret were given
			callback('Bad URL');
		}
		
	});
});

// TODO: create separet namespaces as: /notificaions, /chat etc...
io.sockets.on('connection',function(socket) {
	var secret = socket.manager.handshaken[socket.id].query.secret,
		_redisClient = redis.createClient();
	
	// when the redis client gets a message from the subscribed channels, we are sending back to the user's browser via socket.io
	_redisClient.on('message',function(channel,message) {
		socket.emit('notify',message);
	});
	
	// subscribe to the user's own channel
	_redisClient.subscribe(secret);
	// subscribe to the broadcast channel
	_redisClient.subscribe('broadcast');
	// TODO: subscribe to group channels (a.k.a rooms)
});



