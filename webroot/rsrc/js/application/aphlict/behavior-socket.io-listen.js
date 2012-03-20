/**
 * @provides javelin-behavior-socket.io-listen
 * @requires javelin-behavior
 *           javelin-util
 *           javelin-stratcom
 *           notifications-humane
 *           socket.io
 */

JX.behavior('socket.io-listen', function(config) {
  var socket = io.connect(config.server + ":" + config.port);
  
  socket.on('connected', function () {
    humane.timeout = 2500;
    humane.success.timeout = 0;
    humane.success.clickToClose = true;
    humane.on('hide', function(type, message) {
      if (type == "success") {
	location.reload(true);
      }
    });
    JX.log("Connected to socket.io server " + config.server + " port " + config.port);
  });

  socket.on('notification', function(notification) {
    JX.log(notification);
    if(notification.type == "refresh" 
       && notification.pathname == window.location.pathname) {
      humane.success(notification.info);
    } else if (notification.type == "generic") {
      humane.log(notification.info);
    }
  });
  
});

