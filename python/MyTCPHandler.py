#!/usr/bin/python
import SocketServer
import time

class MyTCPHandler(SocketServer.BaseRequestHandler):

	def handle(self):
		# self.request is the TCP socket connected to the client
		self.data = self.request.recv(1024).strip()
		print "{} wrote:".format(self.client_address[0])
		print self.data
		# just send back the same data, but upper-cased
		#self.request.sendall("bitch")
		self.request.sendall('''HTTP/1.1 101 Web Socket Protocol Handshake\r
		Upgrade: WebSocket\r
		Connection: Upgrade\r
		WebSocket-Origin: http://192.168.10.1:8082\r
		WebSocket-Location: ws://192.168.10.1:9999/\r
		WebSocket-Protocol: sample'''.strip() + '\r\n\r\n')
		time.sleep(1)
		self.send('\x00hello\xff')
		time.sleep(1)
		self.send('\x00world\xff')
		self.close()
		
	

if __name__ == "__main__":
	HOST, PORT = "192.168.10.1", 9999

    # Create the server, binding to localhost on port 9999
	server = SocketServer.TCPServer((HOST, PORT), MyTCPHandler)

    # Activate the server; this will keep running until you
    # interrupt the program with Ctrl-C
	print "listen..."
	server.serve_forever()