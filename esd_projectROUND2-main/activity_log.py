#!/usr/bin/env python3
# The above shebang (#!) operator tells Unix-like environments
# to run this file as a python3 script

import json
import os

from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS

from datetime import datetime
import amqp_setup
import requests
from invokes import invoke_http
from os import environ

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root@localhost:3306/activity'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)

CORS(app)



class Activity(db.Model):
    __tablename__ = 'activity'

    aid = db.Column(db.Integer, primary_key=True)
    source = db.Column(db.String(128), nullable=False)
    datetime = db.Column(db.DateTime, nullable=False, default=datetime.now)
    description = db.Column(db.String(4000), nullable=False)
    

    def json(self):
        return {
            "aid": self.aid,
            "source": self.source, 
            "datetime": self.datetime, 
            "description": self.description, 
        }

monitorBindingKey='#'


def receiveOrderLog():
    amqp_setup.check_setup()
        
    queue_name = 'Activity_Log'
    
    # set up a consumer and start to wait for coming messages
    amqp_setup.channel.basic_consume(queue=queue_name, on_message_callback=callback, auto_ack=True)
    amqp_setup.channel.start_consuming() # an implicit loop waiting to receive messages; 
    #it doesn't exit by default. Use Ctrl+C in the command window to terminate it.

def callback(channel, method, properties, body): # required signature for the callback; no return
    print("\nReceived an log by " + __file__)
    processOrderLog(body,__file__)
    print() # print a new line feed

def processOrderLog(order,source):
    source = source
    description = order

    
    activity = Activity(source=source,description=description)

    try:
        db.session.add(activity)
        db.session.commit()
    except Exception as e:
        return jsonify(
            {
                "code": 500,
                "message": "An error occurred while creating the activity log. " + str(e)
            }
        ), 500

    return jsonify(
        {
            "code": 200,
            "data": activity.json()
        }
    ), 201
    


if __name__ == "__main__":  # execute this program only if it is run as a script (not by 'import')
    print("\nThis is " + os.path.basename(__file__), end='')
    print(": monitoring routing key '{}' in exchange '{}' ...".format(monitorBindingKey, amqp_setup.exchangename))
    with app.app_context():
        receiveOrderLog()
