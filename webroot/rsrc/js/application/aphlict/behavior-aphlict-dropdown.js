/**
 * @provides javelin-behavior-aphlict-dropdown
 * @requires javelin-behavior
 *           javelin-aphlict
 *           javelin-util
 *           javelin-request
 *           javelin-stratcom
 */

JX.behavior('aphlict-dropdown', function(config) {
  var dropdown = JX.$('phabricator-notification-dropdown');
  var indicator = JX.$('phabricator-notification-indicator');
  var request = null;

  dropdown.style.visibility = 'hidden';
  
  JX.Stratcom.listen(
    'click',
    null,
    function(e) {
      dropdown.style.visibility = 'hidden';
    });

  function refresh() {
    if (request) { //already fetching
        console.log("update in progress...");
      return;
    }

    console.log("updating...");
    request = new JX.Request('/notifications/', function(response) {
      indicator.textContent = '' + response.number;
      if (response.number == 0) {
          indicator.style.fontWeight = "";
      } else {
          indicator.style.fontWeight = "bold";
      }
      JX.DOM.setContent(dropdown, JX.$H(response.content));
      request = null;
    });
    request.send();
  }

  refresh();

  JX.DOM.listen(
      dropdown,
      'click',
      null,
      function(e) {
        e.stop();
      });

  JX.DOM.listen(
      indicator,
      'click',
      null,
      function(e) {
        if (dropdown.style.visibility == 'hidden') {
          dropdown.style.visibility = 'visible';
          JX.Stratcom.invoke('notification-update', null, {});
        } else {
          dropdown.style.visibility = 'hidden';
        }
        e.stop();
      });

  JX.Stratcom.listen('notification-update', null, refresh);
});
