#!/usr/bin/python
import time
import tornado.websocket
import Globals
from Globals import Helper
import thread
import json
#from servo import ServoPWM
#import RPi.GPIO as GPIO
#import RPIO
#import RPIO.PWM

class WebSocketServer(tornado.websocket.WebSocketHandler):
    maxInactivityTime = 40
    MAX_ANGLE = 180;
    MIN_ANGLE = 0;
   
    
    def open(self):
        #Globals.COUNT_CLIENTS
        #print "WebSocket opened"
        Helper.log("WebSocket opened")
        Globals.COUNT_CLIENTS  = Globals.COUNT_CLIENTS + 1
        
        # Variable mit Timestamp der letzten Aktion, wird in on_message aktualisiert
        self.latestActivity = time.time()
        
        # Nur 1 Client darf Kamera kontrollieren
        if Globals.COUNT_CLIENTS > 1:
            self.write_message("Camera already in use.. Disconnected!")
            Globals.COUNT_CLIENTS  = Globals.COUNT_CLIENTS - 1
            self.close()
        
        # Pruefen ob der Client inactiv wird
        self.activityThread = thread.start_new_thread(self.check_inactivity,()) 

    def on_message(self, message):
        #global PAN_START_ANGLE,TILT_START_ANGLE
        self.latestActivity = time.time()
        return_message = "Camera moved!"
        try:
           
            result = json.loads(message)
            Helper.log("JSON erhalten: %s" % (result))
            #print result
            delta = float(result['delta'])
            #Horizontale Bewegung
            if result['direction'] == 'h':
                #print "Horizontal:"
                if (Globals.PAN_START_ANGLE + delta) > self.MAX_ANGLE or (Globals.PAN_START_ANGLE + delta) < self.MIN_ANGLE:
                     return_message ="Maximum pan stop reached!"
                else: 
                    Globals.PAN_START_ANGLE = Globals.PAN_START_ANGLE + delta
                    #print "Horizontal: Move to " + str(Globals.PAN_START_ANGLE)
                    Helper.log("Horizontal: Move to " + str(Globals.PAN_START_ANGLE))
                    Globals.panServoPWM.setCommand(Globals.PAN_START_ANGLE)
            elif result['direction'] == 'v':
                print "Vertikal:"
            #Vertikale Bewegung
                if (Globals.TILT_START_ANGLE  + delta) > self.MAX_ANGLE or (Globals.TILT_START_ANGLE  + delta) < self.MIN_ANGLE:
                     return_message ="Maximum tilt stop reached!"
                else:
                    Globals.TILT_START_ANGLE = Globals.TILT_START_ANGLE + delta
                    Helper.log("Vertikal: Move to " + str(Globals.PAN_START_ANGLE))
                    Globals.tiltServoPWM.setCommand(Globals.TILT_START_ANGLE)
            else:
                print "initialize.."
                
            
            # Ergebnis zurueckgeben
            self.write_message(return_message)
        except ValueError:
            #print "invalid json.."
            Helper.log("Received invalid JSON")

    def on_close(self):
        #global COUNT_CLIENTS
        Globals.COUNT_CLIENTS  = Globals.COUNT_CLIENTS - 1
        Helper.log("WebSocket closed")
        #print "WebSocket closed"
        
    def check_inactivity(self):
        try:
            while (time.time() - self.latestActivity) < self.maxInactivityTime:
                print "Inaktivitaet: Client aktiv?"
                time.sleep(5)
            
            Helper.log("Client inactive, diconncted.")
            self.write_message("Inactive.. Disconnected!")
            self.close()
            self.on_close()
        except tornado.websocket.WebSocketClosedError:
            print "Inaktivitaet: Inaktivter Client hat Seite bereits verlassen.."