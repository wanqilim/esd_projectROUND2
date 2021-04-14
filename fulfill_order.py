from flask import Flask, request, jsonify
from flask_cors import CORS

import os, sys
from os import environ

import requests
from invokes import invoke_http

import json

app = Flask(__name__)
CORS(app)


order_URL = environ.get('order_URL') or "http://localhost:5002/order" 
product_URL = environ.get('product_URL') or "http://localhost:5001/product"


@app.route("/fulfill_order", methods=['PUT'])
def fulfill_order():
    # Simple check of input format and data of the request are JSON
        # Simple check of input format and data of the request are JSON
    # do the actual work
    # 1. Send get order info

    if request.is_json:
        try:
            order = request.get_json()
            print("\nReceived an order change in JSON:", order)

            # do the actual work
            # 1. Send order info {cart items}
            result = processFulfillOrder(order)
            print('\n------------------------')
            print('\nresult: ', result)
            return jsonify(result), result["code"]

        except Exception as e:
            # Unexpected error in code
            exc_type, exc_obj, exc_tb = sys.exc_info()
            fname = os.path.split(exc_tb.tb_frame.f_code.co_filename)[1]
            ex_str = str(e) + " at " + str(exc_type) + ": " + fname + ": line " + str(exc_tb.tb_lineno)
            print(ex_str)

            return jsonify({
                "code": 500,
                "message": "place_order.py internal error: " + ex_str
            }), 500

    # if reached here, not a JSON request.
    return jsonify({
        "code": 400,
        "message": "Invalid JSON input: " + str(request.get_data())
    }), 400


def processFulfillOrder(order):
    # 2. Get the order info
    # Invoke the order microservice
    print('\n-----Invoking order microservice-----')
    oid=order['oid']
    
    print(oid)
    
    order_result = invoke_http(order_URL + '/' + str(oid), method='PUT', json=order)
    print('order_result:', order_result)
    
    code = order_result["code"]


    if code not in range(200, 300):
        # Inform the error microservice
        print('\n\n-----Order microservice fails-----')


        # 7. Return error
        return {
            "code": 500,
            "data": {"order_result": order_result},
            "message": "Order retrival fail."
        }
    else:
        # help me chek if this is correct
        pid = order_result['data']['pid']
        
        order_result = invoke_http(product_URL + '/fulfill/' + str(pid), method='PUT')
        
        return order_result

# Execute this program if it is run as a main script (not by 'import')
if __name__ == "__main__":
    print("This is flask " + os.path.basename(__file__) + " for placing an order...")
    app.run(host="0.0.0.0", port=5400, debug=True)
    # Notes for the parameters: 
    # - debug=True will reload the program automatically if a change is detected;
    #   -- it in fact starts two instances of the same flask program, and uses one of the instances to monitor the program changes;
    # - host="0.0.0.0" allows the flask program to accept requests sent from any IP/host (in addition to localhost),
    #   -- i.e., it gives permissions to hosts with any IP to access the flask program,
    #   -- as long as the hosts can already reach the machine running the flask program along the network;
    #   -- it doesn't mean to use http://0.0.0.0 to access the flask program.