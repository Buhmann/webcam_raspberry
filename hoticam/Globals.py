#!/usr/bin/python
import ConfigParser
import logging
import logging.handlers

LOG_FILENAME = "/var/log/hoticam.log"
LOG_LEVEL = logging.INFO

class Helper():
    
    config = ConfigParser.ConfigParser() 
    logger = logging.getLogger(__name__)
    
    @staticmethod
    def get(value):
        return Helper.config.get("DEFAULT",value)

    @staticmethod
    def log(value):
           Helper.logger.info(value)
        

# Configparser initialisieren
Helper.config.read('/home/pi/elektro/webcam/webinterface/files/settings.ini')

# Configure logging to log to a file, making a new file at midnight and keeping the last 3 day's data
       
# Set the log level to LOG_LEVEL
Helper.logger.setLevel(LOG_LEVEL)
# Make a handler that writes to a file, making a new file at midnight and keeping 3 backups
handler = logging.handlers.TimedRotatingFileHandler(LOG_FILENAME, when="midnight", backupCount=3)
# Format each log message like this
formatter = logging.Formatter('%(asctime)s %(levelname)-8s %(message)s')
# Attach the formatter to the handler
handler.setFormatter(formatter)
# Attach the handler to the logger
Helper.logger.addHandler(handler)
     
#print Helper.getSetting('HotiCam', 'PAN_START_ANGLE')

# Sartposition Horizontal
PAN_START_ANGLE = float(Helper.get("pan_start_angle"))
# Startposition Vertikal
TILT_START_ANGLE = float(Helper.get("tilt_start_angle"))

# nicht aendern
COUNT_CLIENTS = 0
panServoPWM = None
tiltServoPWM = None


