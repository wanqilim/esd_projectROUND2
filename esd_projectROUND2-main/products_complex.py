from flask import Flask, render_template, redirect, url_for, request, jsonify, session
import requests
import sys
import hashlib

from flask_cors import CORS

import os
import sys
from os import environ

import requests
from invokes import invoke_http


app = Flask(__name__)
app.config["DEBUG"] = True
CORS(app)


@app.route("/product/<string:pid>")
def show_listing(pid):
    return render_template('product_listing.html')

if __name__ == "__main__":
    app.run(host="0.0.0.0", port="5000")