from flask import Flask, request, jsonify
from flask_cors import CORS

import os, sys
from os import environ

import requests
from invokes import invoke_http

import amqp_setup
import pika
import json

app = Flask(__name__)
CORS(app)


order_URL = environ.get('order_URL') or "http://localhost:5002/order" 
product_URL = environ.get('product_URL') or "http://localhost:5001/product" 
customer_URL = environ.get('customer_URL') or "http://localhost:5003/customer" 

@app.route("/check_order_biz/<string:bid>", methods=['GET'])
def check_order_biz(bid):
    # Simple check of input format and data of the request are JSON
    # do the actual work
    # 1. Send get order info
    result = processCheckOrderBiz(bid)
    print('\n------------------------')
    print('\nresult: ', result)
    return jsonify(result), result["code"]

def processCheckOrderBiz(bid):

    print('\n\n-----Invoking product microservice-----')
    # 2. get pid based on bid

    product_result = invoke_http(
        (product_URL + '/business/' + bid), method="GET" )
    print("product:", product_result, '\n')

    # Check the shipping result;
    # if a failure, send it to the error microservice.
    code = product_result["code"]
    message = json.dumps(product_result)
    amqp_setup.check_setup()

    #amqp_setup.check_setup()

    if code not in range(200, 300):
        # Inform the error microservice
        #print('\n\n-----Invoking error microservice as shipping fails-----')
        #print('\n\n-----Publishing the (product error)')
        message=str(bid)+'Product microservice fail'
        amqp_setup.channel.basic_publish(exchange=amqp_setup.exchangename, routing_key="product.error", 
            body=message, properties=pika.BasicProperties(delivery_mode = 2)) 
        # make message persistent within the matching queues until it is received by some receiver 
        # (the matching queues have to exist and be durable and bound to the exchange)

        # - reply from the invocation is not used;
        # continue even if this invocation fails        
        #print("\nProduct status ({:d}) published to the RabbitMQ Exchange:".format(
        #    code), product_result)

        # 7. Return error
        return {
            "code": 500,
            "data": {
                "product_result": product_result},
            "message": "Product retrival fail."
        }

    else:
        # 4. Record new order
        # record the activity log anyway
        #print('\n\n-----Invoking activity_log microservice-----')
        #print('\n\n-----Publishing the (product info) message with routing_key=product.info-----')        
        message=str(bid)+'Product microservice success'
        amqp_setup.channel.basic_publish(exchange=amqp_setup.exchangename, routing_key="product.info", body=message)

    # 3. Get the order info base on pid
    # Invoke the order microservice
    product_result_list = product_result['data']['products']
    #print(product_result_list)
    print('\n-----Invoking order microservice-----')
    

    final_result_list=list()
    
    for product in product_result_list:
        pid=product['pid']
        order_result = invoke_http(order_URL + '/product/' + str(pid))
        #print('order_result:', order_result)
    
        # Check the order result; if a failure, print error.
        code = order_result["code"]

        if code not in range(200, 300):
            if code != 404:

                #  Return error
                print('\n\n-----Publishing the (order error) message with routing_key=order.error-----')

                # invoke_http(error_URL, method="POST", json=shipping_result)
                message = json.dumps(order_result)
                message=str(pid)+'Order microservice fail'
                amqp_setup.channel.basic_publish(exchange=amqp_setup.exchangename, routing_key="order.error", 
                    body=message, properties=pika.BasicProperties(delivery_mode = 2))

                #print("\nOder status ({:d}) published to the RabbitMQ Exchange:".format(
                #    code), order_result)

        else:
            # 4. Confirm success
            #print('\n\n-----Publishing the (order info) message-----')
            message=str(pid)+'Order microservice success'
            amqp_setup.channel.basic_publish(exchange=amqp_setup.exchangename, routing_key="order.info", 
            body=message)
            #print(order_result_list)


            # 5. Send PID order to product
            # Invoke the product microservice

            #print('\n\n-----Invoking customer microservice-----')

            #print(product)
            orders=order_result['data']['order']
            #print(orders)
            for order in orders:
                #print(order)
                cid=order['cid']
                customer_result = invoke_http(
                    (customer_URL + '/location/' + str(cid)), method="GET")
                #print("customer:", customer_result, '\n')
                
                # Check the customer result;
                # if a failure, send it to the error microservice.
                code = customer_result["code"]
                if code not in range(200, 300):
                    # Inform the error microservice
                    #print('\n\n-----Publishing the (customer error)')
                    #message = json.dumps(customer_result)
                    message=str(cid)+'Customer microservice fail'
                    amqp_setup.channel.basic_publish(exchange=amqp_setup.exchangename, routing_key="customer.error", 
                        body=message, properties=pika.BasicProperties(delivery_mode = 2)) 
                    print("\nCustomer status ({:d}) published to the RabbitMQ Exchange:".format(code), customer_result)            
                    # 7. Return error
                    return {
                        "code": 400,
                        "data": {
                            "order_result": order_result,
                            "shipping_result": product_result,
                            "customer_result":customer_result
                        },
                        "message": "Customer retrival fail."
                    }

                else:
                    #print('\n\n-----Publishing the (order info) message-----')
                    message=str(cid)+'Customer microservice success'
                    amqp_setup.channel.basic_publish(exchange=amqp_setup.exchangename, routing_key="customer.info", 
                    body=message)
                    final_result_list.append({'pname':product['pname'], 'imgname':product['imgname'], 'dStatus': order['dStatus'], 'oid': order['oid'],'group_oid': order['group_oid'], 'datetime':order['datetime'], 'oStatus': order['oStatus'], 'quantity': order['quantity'], 'dStatus': order['dStatus'], 'address': customer_result['data']['address']})

    sorted_finallist = sorted(final_result_list, key=lambda k: k['group_oid'])

    # 7. Return created order, shipping record
    return {
        "code": 201,
        "data": {
            "required_info": final_result_list
        }
    }






# Execute this program if it is run as a main script (not by 'import')
if __name__ == "__main__":
    print("This is flask " + os.path.basename(__file__) + " for checking order by business...")
    app.run(host="0.0.0.0", port=5100, debug=True)
    # Notes for the parameters: 
    # - debug=True will reload the program automatically if a change is detected;
    #   -- it in fact starts two instances of the same flask program, and uses one of the instances to monitor the program changes;
    # - host="0.0.0.0" allows the flask program to accept requests sent from any IP/host (in addition to localhost),
    #   -- i.e., it gives permissions to hosts with any IP to access the flask program,
    #   -- as long as the hosts can already reach the machine running the flask program along the network;
    #   -- it doesn't mean to use http://0.0.0.0 to access the flask program.
