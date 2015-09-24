#!/usr/bin/python
import time; 
import RPi.GPIO as GPIO

SENSOR_PIN = 24

GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
GPIO.setup(SENSOR_PIN,GPIO.IN)

print "Eimer-Sensor Test"
print "GPIO PIN: " + str(SENSOR_PIN)
input_sensor  = GPIO.input(SENSOR_PIN)
if input_sensor == 1:
	print "Eimer leer"
else:
	print "Eimer voll" 

print "Return Wert: " + str(input_sensor)
		
