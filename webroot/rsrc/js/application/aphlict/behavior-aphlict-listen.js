/**
 * @provides javelin-behavior-aphlict-listen
 * @requires javelin-behavior
 *           javelin-aphlict
 *           javelin-util
 *           javelin-stratcom
 *           javelin-behavior-aphlict-dropdown
 *           notification-humane
 */

JX.behavior('aphlict-listen', function(config) {
  function onready() {
    JX.log("The flash component is ready!");
    JX.log("Trying to connect to " + config.server + " port " + config.port);

    humane.timeout = 2500;
    humane.success.timeout = 0;
    humane.success.clickToClose = true;
    humane.on('hide', function(type, message) {
      if (type == "success") {
        location.reload(true);
      }
    });

    var client = new JX.Aphlict(config.id, config.server, config.port)
      .setHandler(function(type, message) {
        JX.log("Got aphlict event '" + type + "':");
        if (message) {
          JX.log(message);

          if (type == "receive") {
            if (message.type == "refresh"
                && message.pathname == window.location.pathname) {
              humane.success(message.info);
            } else if (message.type == "generic") {
              JX.Stratcom.invoke('notification-update', null, {});
              // if (message.pathname != window.location.pathname) {
              //   humane.log(message.info);
              // }
            }
          }
        }
      })
      .start();
  }


  // Wait for the element to load, and don't do anything if it never loads.
  // If we just go crazy and start making calls to it before it loads, its
  // interfaces won't be registered yet.
  JX.Stratcom.listen('aphlict-component-ready', null, onready);
});
