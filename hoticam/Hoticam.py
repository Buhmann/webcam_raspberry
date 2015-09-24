#!/usr/bin/python
import RPi.GPIO as GPIO
import RPIO
import RPIO.PWM

import tornado.ioloop
import tornado.web
import tornado.websocket
import thread
import time
import subprocess
import signal
import sys

from servo import ServoPWM
from PicamThread import PicamThread
from WebSocketServer import WebSocketServer
from CameraStream import CameraStream
import Globals
from Globals import Helper

PAN_PWM_PIN = 18
TILT_PWM_PIN = 24

PWM_FREQUENCY = 50    # Hz
PWM_SUBCYLCLE_TIME_US = 1000/PWM_FREQUENCY * 1000
PWM_PULSE_INCREMENT_US = 10
PWM_DMA_CHANNEL = 0


picamThread = None
cameraStream = None

def switchStreamOrMotion():
    global picamThread
    global cameraStream
    picamThread = PicamThread()
    cameraStream = CameraStream()
    
    while (True):
        if Globals.COUNT_CLIENTS > 0:
            if not cameraStream.isAlive():
                Helper.log("Stope Bewegungserkennung")
                picamThread.stop()
                picamThread.join()
                Helper.log("Schalte um auf Streaming")
                cameraStream.start()
            else:
                Helper.log("Stream laeuft..")
        else:
            #Wenn Stream laeuft, beenden
            if cameraStream.isAlive():
                cameraStream.stop()
            #Wenn Thread beendet wurde, neuen Thread erstellen
            if picamThread.stopped():
                picamThread = PicamThread()
            if (not picamThread.isAlive()):
                Helper.log("Stoppe Streaming")
                picamThread.start()
                Helper.log("Schalte um auf Bewegungserkennung")
            else:
                #print "motion laeuft bereits!"
                Helper.log("Bewegungserkennung laeuft")
        time.sleep(5)
   
def signal_handler(signal, frame):
    exitThreads()

def exitThreads():
    Helper.log("Hoticam duch Signal beendet")
    global picamThread
    global cameraStream
    print('You pressed Ctrl+C!')
    # Threads beenden
    try:
        if cameraStream.isAlive():
            cameraStream.stop()
            cameraStream.join()
    except AttributeError:
        Helper.log("CameraStream Undefined..")
    try:
        if picamThread.isAlive():
            picamThread.stop()
    except AttributeError:
        print "picamThread Undefined.."
        Helper.log("picamThread Undefined..")
    sys.exit(0)
    

    
application = tornado.web.Application([
    (r"/", WebSocketServer),
])

if __name__ == "__main__":
    try:
       
        Helper.log("Starte Hoticam")

        
        # Create ServoPWM instances to control the servos
       
        Globals.tiltServoPWM = ServoPWM( TILT_PWM_PIN, 
            minAnglePulseWidthPair=( 0.0, 2350 ), 
            midAnglePulseWidthPair=( 90.0, 1500 ), 
            maxAnglePulseWidthPair=( 180.0, 500.0 ) )
        Globals.panServoPWM = ServoPWM( PAN_PWM_PIN, 
            minAnglePulseWidthPair=( 0.0, 610.0 ), 
            midAnglePulseWidthPair=( 90.0, 1500 ), 
            maxAnglePulseWidthPair=( 180.0, 2350 ) )
        
        
        # Setup RPIO, and prepare for PWM signals
        RPIO.setmode( RPIO.BCM )
        RPIO.PWM.setup( pulse_incr_us=PWM_PULSE_INCREMENT_US )
        RPIO.PWM.init_channel( PWM_DMA_CHANNEL, PWM_SUBCYLCLE_TIME_US )
        
        
        #workaround, programm wird sonst bei Aufruf von subprocess in PicamThread komplett beendet
        signal.signal(signal.SIGCHLD, signal.SIG_IGN)
        #ctrl + c handler um threads zu beenden
        signal.signal(signal.SIGINT, signal_handler)
        #wenn dienst ueber daemon-start-stop beendet wird 
        signal.signal(signal.SIGTERM, signal_handler)
    
        # Startposition einnehmen
        Globals.panServoPWM.setCommand(Globals.PAN_START_ANGLE)
        Globals.tiltServoPWM.setCommand(Globals.TILT_START_ANGLE)
        
        # Thread der Stream oder Bewegungserkennung schaltet, Stream wird aktiviert sobald 1 Besucher auf der Seite ist
        thread.start_new_thread(switchStreamOrMotion,()) 
        
    
        Helper.log("Initialisierung beendet")
        # Websocket Listen
        application.listen(9998)
        tornado.ioloop.IOLoop.instance().start()	
        
    
    except Exception as e:
    
        print "Got exception", e
        
    finally:
        exitThreads()
        Helper.log("Beenden..")
        time.sleep(5)
        
            
        RPIO.PWM.clear_channel_gpio( PWM_DMA_CHANNEL, Globals.panServoPWM.pwmPin )
        RPIO.PWM.clear_channel_gpio( PWM_DMA_CHANNEL, Globals.tiltServoPWM.pwmPin )
        
        RPIO.PWM.cleanup()
        RPIO.cleanup()
