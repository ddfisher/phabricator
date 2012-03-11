/**
 * @provides javelin-behavior-aphlict-listen
 * @requires javelin-behavior
 *           javelin-aphlict
 *           javelin-util
 *           javelin-stratcom
 *           notifications-humane
 */

JX.behavior('aphlict-listen', function(config) {
  function onready() {
    JX.log("The flash component is ready!");

    humane.timeout = 0;
    humane.clickToClose = true;
    humane.on('hide', function(type, message) {
      location.reload(true);
    });

    var client = new JX.Aphlict(config.id, config.server, config.port)
      .setHandler(function(type, message) {
        if (message) {
          JX.log("Got aphlict event '" + type + "':");
          JX.log(message);

          if (type == "receive") {
            if (message.type == "refresh"
              && message.pathname == window.location.pathname) {
                humane.info(message.info);
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
