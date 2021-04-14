from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS

from datetime import datetime
import json
from os import environ

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root@localhost:3306/order'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
app.config['SQLALCHEMY_ENGINE_OPTIONS'] = {'pool_recycle': 299}

db = SQLAlchemy(app)

CORS(app)


class Order(db.Model):
    __tablename__ = 'order'

    oid = db.Column(db.Integer, primary_key=True)
    pid = db.Column(db.Integer, nullable=False)
    group_oid = db.Column(db.Integer, nullable=False)
    quantity = db.Column(db.Integer, nullable=False)
    cid = db.Column(db.Integer, nullable=False)
    datetime = db.Column(db.DateTime, nullable=False, default=datetime.now)
    oStatus = db.Column(db.Integer, nullable=False)
    dStatus = db.Column(db.String(128), nullable=False)
    pResponse = db.Column(db.String(128), nullable=False)

    def json(self):

        return {
            "oid": self.oid,
            "group_oid": self.group_oid,
            "pid": self.pid,
            "quantity": self.quantity,
            "cid": self.cid,
            "datetime": self.datetime,
            "oStatus": self.oStatus,
            "dStatus": self.dStatus,
            "pResponse": self.pResponse
        }


@app.route("/order")
def get_all():
    orderlist = Order.query.all()
    if len(orderlist):
        return jsonify(
            {
                "code": 200,
                "data": {
                    "order": [order.json() for order in orderlist]
                }
            }
        )
    return jsonify(
        {
            "code": 404,
            "message": "There are no orders."
        }
    ), 404


@app.route("/order/<string:oid>")
def find_by_oid(oid):
    order = Order.query.filter_by(oid=oid).first()
    if order:
        return jsonify(
            {
                "code": 200,
                "data": order.json()
            }
        )
    return jsonify(
        {
            "code": 404,
            "message": "order not found."
        }
    ), 404


@app.route("/order/product/<string:pid>")
def find_by_pid(pid):
    orderlist = Order.query.filter_by(pid=pid).all()
    if len(orderlist):
        return jsonify(
            {
                "code": 200,
                "data": {
                    "order": [order.json() for order in orderlist]
                }
            }
        )
    return jsonify(
        {
            "code": 404,
            "message": "There are no orders."
        }
    ), 404

@app.route("/order/customer/<string:cid>")
def find_by_cid(cid):
    orderlist = Order.query.filter_by(cid=cid).all()
    if len(orderlist):
        return jsonify(
            {
                "code": 200,
                "data": {
                    "order": [order.json() for order in orderlist]
                }
            }
        )
    return jsonify(
        {
            "code": 404,
            "message": "There are no orders."
        }
    ), 404

@app.route("/order", methods=['POST'])
def create_order():
    print(request.json)
    pidlist = request.json.get('newOrder', None)
    cid = request.json.get('cid', None)
    pResponse = request.json.get('pResponse', None)
    oStatus = 0
    dStatus = "Unfulfilled"
    
    orderlist = Order.query.all()
    if len(orderlist) == 0:
        group_oid = 1
    else:

        lastrec= Order.query.order_by(Order.group_oid.desc()).first()
        group_oid = int(lastrec.json()['group_oid']) + 1

    for pid in pidlist:

        order = Order(pid=pid,  group_oid=group_oid, cid=cid, oStatus=oStatus, dStatus=dStatus, pResponse=pResponse)

        try:
            db.session.add(order)
            db.session.commit()
        except Exception as e:
            return jsonify(
                {
                    "code": 500,
                    "message": "An error occurred while creating the order. " + str(e)
                }
            ), 500

    # print(json.dumps(product.json(), default=str)) # convert a JSON object to a string and print
    # print()

    return jsonify(
        {
            "code": 201,
            "data": order.json()
        }
    ), 201


@app.route("/order/<string:group_oid>", methods=['PUT'])
def update_order(group_oid):
    try:
        orders = Order.query.filter_by(group_oid=group_oid)
        if not orders:
            return jsonify(
                {
                    "code": 404,
                    "data": {
                        "group_oid": group_oid
                    },
                    "message": "Order not found."
                }
            ), 404

        # update status
        data = request.get_json()
        returnlist = []
        for order in orders:
            if data['dStatus']:
                order.dStatus = data['dStatus']
                db.session.commit()
            returnlist.append(order.json())
        return jsonify(
            {
                "code": 200,
                "data": returnlist
            }
        ), 200
    except Exception as e:
        return jsonify(
            {
                "code": 500,
                "data": {
                        "group_oid": group_oid
                },
                "message": "An error occurred while updating the order. " + str(e)
            }
        ), 500


@app.route("/order/<string:oid>", methods=['DELETE'])
def delete_product(oid):
    order = Order.query.filter_by(oid=oid).first()
    if order:
        db.session.delete(order)
        db.session.commit()
        return jsonify(
            {
                "code": 200,
                "data": {
                    "oid": oid
                }
            }
        )
    return jsonify(
        {
            "code": 404,
            "data": {
                "oid": oid
            },
            "message": "Order not found."
        }
    ), 404


if __name__ == '__main__':
    app.run(port=5002, debug=True)
