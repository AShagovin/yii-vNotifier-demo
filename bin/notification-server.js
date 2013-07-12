(function() {
  var api, app, config, express, io, redis, server;

  config = require('./config.js');

  redis = require('redis');

  express = require('express');

  app = express();

  app.use(express.bodyParser());

  api = require('./notification-api.js').init(config);

  app.post('/generateusertoken', api.routes().generateusertoken);

  app.post('/getusertoken', api.routes().getusertoken);

  app.post('/publish', api.routes().publish);

  server = require('http').createServer(app);

  io = require('socket.io').listen(server, {
    'origins': "*:*"
  });

  io.configure(function() {
    var redisClient;
    redisClient = redis.createClient(config.redis.port, config.redis.host);
    return io.set('authorization', function(handshakeData, callback) {
      if (handshakeData.query.token) {
        if (redisClient.get(handshakeData.query.token)) {
          return callback(null, true);
        } else {
          return callback(null, false);
        }
      } else {
        return callback('Bad URL');
      }
    });
  });

  io.sockets.on('connection', function(socket) {
    var token, _redisClient;
    token = socket.manager.handshaken[socket.id].query.token;
    console.log('=== token: ' + token);
    _redisClient = redis.createClient(config.redis.port, config.redis.host);
    _redisClient.on('message', function(channel, message) {
      return socket.emit('notify', JSON.parse(message));
    });
    _redisClient.subscribe(token);
    return _redisClient.subscribe('broadcast');
  });

  server.listen(config.port || 4000, null);

}).call(this);
