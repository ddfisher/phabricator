var net = require('net');
var http  = require('http');
var url = require('url');
var querystring = require('querystring');




function getFlashPolicy() {
  return [
    '<?xml version="1.0"?>',
    '<!DOCTYPE cross-domain-policy SYSTEM ' +
      '"http://www.macromedia.com/xml/dtds/cross-domain-policy.dtd">',
    '<cross-domain-policy>',
    '<allow-access-from domain="*" to-ports="2600"/>',
    '</cross-domain-policy>'
  ].join("\n");
}

net.createServer(function(socket) {
  socket.on('data', function() {
    socket.write(getFlashPolicy() + '\0');
  });
}).listen(843);



function write_json(client, data) {
  if(client.status == 'connected') {
    var serial = JSON.stringify(data);
    var length = Buffer.byteLength(serial, 'utf8');
    length = length.toString();
    while (length.length < 8) {
      length = "0" + length;
    }
    client.socket.write(length + serial);
  }
}


var clients = {};
var num_connections = 0;
var current_id = 0;
// According to the internet up to 2^53 can
// be stored in javascript, this is less than that
var BIG_NUMBER = 9007199254740991;//2^53 -1

// If we get one connections per millisecond this will
// be fine as long as someone doesn't maintain a
// connection for longer than 6854793 years.  If
// you want to write something pretty be my guest

function generate_id() {
  if(current_id > BIG_NUMBER ) {
    current_id = 0;
  }
  return current_id++;
}

var send_server = net.createServer(function(socket) {
  client_id = generate_id();
  clients[client_id] = {
    id: client_id,
    socket: socket,
    status: 'init'
  };
  socket.on('connect', function() {
    clients[client_id].status = 'connected';
    num_connections++;
    console.log(client_id + ": send_server connect");
  });

  socket.on('close', function() {
    delete clients[client_id];
    num_connections--;
  });
}).listen(2600);



var receive_server = http.createServer(function(request, response) {
  response.writeHead(200, {'Content-Type' : 'text/plain'});

  if (request.method == 'POST') {
    var body = '';
    request.on('data', function (data) {
      body += data;
    });
    request.on('end', function () {
      var data = querystring.parse(body);
      data.pathname = data.pathname.replace(/^\s+|\s+$/g, '');
      broadcast(data);
      console.log(data);
      response.end();
    });
  }
}).listen(22281, '127.0.0.1');


// TODO Add admin interface to view server stats
// var status_server = http.createServer(function(request, response) {
//   response.writeHead(200, {'Content-Type' : 'text/plain'});
//   var stats = [];
//   stats.push("Number of Clients Connected: " + num_connections);
//   stats.push("Next ID: " + current_id);

//   response.end(stats.join("\n"));
// }).listen(22282, '127.0.0.1');

function broadcast(data) {
  for(var k in clients) {
    write_json(clients[k], data);
  }
}

