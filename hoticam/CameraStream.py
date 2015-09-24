#!/usr/bin/python
import subprocess
import time
import shlex
import os
import signal
from Globals import Helper

class CameraStream():
    
    def __init__(self):
        self.alive = False
        self.process = None
        self.FNULL = None
        self.command = "raspivid -t 9999999 -w {} -h {} -fps {} -b {}  -o - | ffmpeg -i - -vcodec copy -an -r 25 -f flv -metadata streamName=myStream tcp://0.0.0.0:6666".format(Helper.get("width"),Helper.get("height"),Helper.get("fps"),Helper.get("bitrate"))
        #self.command = "raspivid -t 9999999 -w 640 -h 480 -fps 20 -b 500000  -o - | ffmpeg -i - -vcodec copy -an -r 25 -f flv -metadata streamName=myStream tcp://0.0.0.0:6666"
        
    
    def start(self):
        self.FNULL = open(os.devnull, 'w')
        self.process = subprocess.Popen(self.command,shell=True,stdout=self.FNULL, stderr=subprocess.STDOUT,preexec_fn=os.setsid)
        self.alive = True
        self.hasStopped = False
   
    #def stop(self):
    #    self.hasStopped = True
        
    def stop(self):
        if self.alive:
            os.killpg(self.process.pid, signal.SIGTERM) 
            self.FNULL.close()
            self.alive = False
        
    def isAlive(self):
        return self.alive
