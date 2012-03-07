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

    var client = new JX.Aphlict(config.id, config.server, config.port)
      .setHandler(function(type, message) {
        if (message) {
          JX.log("Got aphlict event '" + type + "':");
          JX.log(message);
        } else {
          JX.log("Got aphlict event '" + type + "'.");
        }
        if (type == "receive") {
            humane.timeout = 0;
            humane.clickToClose = true;
            humane.on('hide', function(type, message) {
                location.reload(true);
            });
            humane.info("Page Updated, Please Refresh");
        }
      })
      .start();
  }


  // Wait for the element to load, and don't do anything if it never loads.
  // If we just go crazy and start making calls to it before it loads, its
  // interfaces won't be registered yet.
  JX.Stratcom.listen('aphlict-component-ready', null, onready);
});
