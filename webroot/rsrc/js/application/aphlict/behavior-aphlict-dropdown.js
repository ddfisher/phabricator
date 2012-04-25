/**
 * @provides javelin-behavior-aphlict-dropdown
 * @requires javelin-behavior
 *           javelin-aphlict
 *           javelin-util
 *           javelin-request
 *           javelin-stratcom
 */

JX.behavior('aphlict-dropdown', function(config) {
  var open = false;
  var dropdown = JX.$('phabricator-notification-dropdown');
  var indicator = JX.$('phabricator-notification-indicator');
  var request = null;
 
  function refresh() {
    if (request) { //already fetching
        console.log("update in progress...");
      return;
    }

    console.log("updating...");
    request = new JX.Request('/notifications/', function(response) {
      indicator.textContent = '' + response.number;
      if (response.number == 0) {
          // indicator.style.color = "#ffffff";
          indicator.style.fontWeight = "";
      } else {
          // indicator.style.color = "#D10000";
          indicator.style.fontWeight = "bold";
      }
      JX.DOM.setContent(dropdown, JX.$H(response.content));
      request = null;
    });
    request.send();
  }

  refresh();

  indicator.onclick = function() {
    if (open) {
      dropdown.style.height = "0px";
    } else {
      dropdown.style.height = "300px";
      JX.Stratcom.invoke('notification-update', null, {});
    }
    open = !open;
  };
  
  JX.Stratcom.listen('notification-update', null, refresh);
});
