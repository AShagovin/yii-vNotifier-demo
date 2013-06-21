(function() {
  var api, app, config, crypto, express, io, redis, redisClient, server, sockets;

  config = require('./config.js');

  redis = require('redis');

  redisClient = redis.createClient(config.redis.port, config.redis.host);

  crypto = require('crypto');

  express = require('express');

  app = require('express')();

  server = require('http').createServer(app);

  app.use(express.bodyParser());

  api = require('./notification-api.js').init(app, config);

  io = require('socket.io').listen(server, {
    'origins': "*:*"
  });

  sockets = {};

  io.configure(function() {
    return io.set('authorization', function(handshakeData, callback) {
      if (handshakeData.query.secret) {
        if (redisClient.get(handshakeData.query.secret)) {
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
    var secret, _redisClient;
    secret = socket.manager.handshaken[socket.id].query.secret;
    _redisClient = redis.createClient(config.redis.port, config.redis.host);
    _redisClient.on('message', function(channel, message) {
      return socket.emit('notify', JSON.parse(message));
    });
    _redisClient.subscribe(secret);
    return _redisClient.subscribe('broadcast');
  });

  server.listen(config.port || 4000, null);

}).call(this);
