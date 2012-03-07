var net = require('net');
var http  = require('http');
var url = require('url');

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


var page_updated = false;
var obj_id = null;

var flash_socket = null;
var num_connections = 0;

function json_write(socket, data) {
    var serial = JSON.stringify(data);
    var length = Buffer.byteLength(serial, 'utf8');
    length = length.toString();
    while (length.length < 8) {
      length = "0" + length;
    }
    socket.write(length + serial);
    console.log('write : ' + length + serial);
}



var sp_server = net.createServer(function(socket) {
    flash_socket = socket;
    console.log(socket.address());
    socket.on('connect', function() {
    	console.log(socket.remoteport);
    });
}).listen(2600);

var status_server = http.createServer(function(request, response) {
    response.writeHead(200, {'Content-Type' : 'text/plain'});

    if (request.method == 'GET') {
	response.end("Sweet");
	var output = request.url;
	obj_id = output.substring(1);
	if(flash_socket) {
	    json_write(flash_socket, {phid: obj_id});
	}
    } else if (request.method == 'POST') {
	response.end('POST');
    } else {
	response.end('Really Bitch, a delete request?!?');
    }
}).listen(22281, '127.0.0.1');
