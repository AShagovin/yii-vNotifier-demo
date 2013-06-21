(function() {
  var NotificationApi;

  NotificationApi = (function() {
    var crypto, redis;

    redis = require('redis');

    crypto = require('crypto');

    function NotificationApi(app, config) {
      var self;
      this.config = config;
      self = this;
      app.post('/generateusersecret', function(req, res) {
        return self.handleAPIRequest(req, res, function(req, res) {
          return self.redisClient().get(req.headers.host + '_' + req.body.user_id, function(err, reply) {
            if (reply) {
              return self.jsonResponse(res, {
                userSecret: reply
              });
            } else {
              console.log('genToken call');
              return self.genToken(req.headers.host + '_' + req.body.user_id, res);
            }
          });
        });
      });
      app.post('/getusersecret', function(req, res) {
        return self.handleAPIRequest(req, res, function(req, res) {
          return self.redisClient().get(req.headers.host + '_' + req.body.user_id, function(err, reply) {
            return self.jsonResponse(res, {
              userSecret: reply
            });
          });
        });
      });
      app.post('/publish', function(req, res) {
        return self.handleAPIRequest(req, res, function(req, res) {
          self.redisClient().publish(req.body.channel, req.body.message);
          return self.jsonResponse(res, {});
        });
      });
    }

    NotificationApi.prototype.handleAPIRequest = function(req, res, callback) {
      if ((req.body.__app_secret__ != null) && req.body.__app_secret__ === 'nonexistent') {
        return callback(req, res);
      } else {
        res.writeHead(403, {
          'Content-Type': 'text/plain'
        });
        return res.end('Not authorized');
      }
    };

    NotificationApi.prototype.jsonResponse = function(res, obj) {
      res.writeHead(200, {
        'Content-Type': 'application/json'
      });
      return res.end(JSON.stringify(obj));
    };

    NotificationApi.prototype.genToken = function(prefix, res) {
      var self;
      self = this;
      return crypto.randomBytes(48, function(ex, buf) {
        var token;
        token = buf.toString('base64').replace(/\//g, '_').replace(/\+/g, '-');
        return self.redisClient().get(token, function(err, reply) {
          if (reply) {
            return genToken(prefix, res);
          } else {
            self.redisClient().set(prefix, token);
            return self.jsonResponse(res, {
              userSecret: token
            });
          }
        });
      });
    };

    NotificationApi.prototype.redisClient = function() {
      console.log(this.config);
      return redis.createClient(this.config.redis.port, this.config.redis.host);
    };

    return NotificationApi;

  })();

  module.exports.init = function(app, config) {
    return new NotificationApi(app, config);
  };

}).call(this);
