var config = require('./config.js'),
	http = require('http'),
	redis = require('redis'),
	redisClient = redis.createClient(config.redis.port,config.redis.host),
	url = require('url'),
	crypto = require('crypto');

var app = http.createServer(function (req, res) {
	if(req.method == "OPTIONS") {
		 res.header('Access-Control-Allow-Origin', '*:*');
		 res.send(200);
	} else {

		var u = url.parse(req.url,true),
			body = '';
			
		req.on('data',function(chunk) {
			body += chunk;
		});

		req.on('end',function() {
			if(body) {
				var data =JSON.parse(body);
				if(data.__app_secret__ && data.__app_secret__ == 'nonexistent') {
					switch(u.pathname) {
						case '/generateusersecret' :
							redisClient.get(req.headers.host + '_' + data.user_id,function(err,reply) {
								if(reply) {
									jsonResponse(res,{userSecret : reply});
								} else {
									genToken(req.headers.host + '_' + data.user_id,res);
								}
							});
						break;
						case '/getusersecret' :
							redisClient.get(req.headers.host + '_' + data.user_id,function(err,reply) {
								jsonResponse(res,{userSecret : reply});
							});
						break;
						case '/publish':
							redisClient.publish(data.channel,data.message);
							jsonResponse(res,{});
						break;
						default : 
							jsonResponse(res,{error : "Unknown Command: " + u.pathname});
						break
					}
				} else {
					res.writeHead(403, {'Content-Type': 'text/plain'});
					res.end('Not authorized');
				}
			}
		});
	}
});
app.listen(config.port || 4000, null);

var	io = require('socket.io').listen(app,{
		'origins' : "*:*"
	}),
	sockets = {};

io.configure(function() {
	// set authorization
	io.set('authorization',function(handshakeData,callback) {
		if(handshakeData.query.secret) {
			// when the user's secret is in redis then we trust him as an authenticated user
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

// @TODO: create separeta namespaces as: /notificaions, /chat etc...
io.sockets.on('connection',function(socket) {
	var secret = socket.manager.handshaken[socket.id].query.secret,
		_redisClient = redis.createClient(config.redis.port,config.redis.host);
	
	// when the redis client gets a message from the subscribed channels, we are sending back to the user's browser via socket.io
	_redisClient.on('message',function(channel,message) {
		socket.emit('notify',JSON.parse(message));
	});
	
	// subscribe to the user's own channel
	_redisClient.subscribe(secret);
	// subscribe to the broadcast channel
	_redisClient.subscribe('broadcast');
	
	// TODO: subscribe to group channels (a.k.a rooms)
});


function jsonResponse(res,obj) {
	res.writeHead(200, {'Content-Type': 'application/json'});
	res.end(JSON.stringify(obj));
}

function genToken(prefix,res) {
	crypto.randomBytes(48,function(ex,buf) {
		var token = buf.toString('base64').replace(/\//g,'_').replace(/\+/g,'-');

		redisClient.get(token,function(err,reply) {
			if(reply) {
				genToken(prefix,res);
			} else {
				redisClient.set(prefix,token);
				jsonResponse(res,{userSecret : token});
			}
		});
	});

}





