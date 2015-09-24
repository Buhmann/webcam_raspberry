#!/usr/bin/python
import time;
import picamera;
import os;

camera = picamera.PiCamera()
camera.hflip = True
camera.vflip = True
while True:
	print("foto..")
	camera.capture('/home/pi/elektro/webcam/webinterface/bild/tmp.jpg')
	os.rename('/home/pi/elektro/webcam/webinterface/bild/tmp.jpg','/home/pi/elektro/webcam/webinterface/bild/image.jpg')
	#time.sleep(0.5)

