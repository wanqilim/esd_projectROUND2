from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
import hashlib


app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root@localhost:3306/business'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)

CORS(app)


class Business(db.Model):
    __tablename__ = 'business'

    bid = db.Column(db.Integer, primary_key=True)
    bname = db.Column(db.String(128), nullable=False)
    email = db.Column(db.String(128), nullable=False)
    password = db.Column(db.String(128), nullable=False)
    paypal = db.Column(db.String(128), nullable=False)
    address = db.Column(db.String(128), nullable=False)
    bdescription = db.Column(db.String(128), nullable=False)
    

    def json(self):
        return {
            "bid": self.bid,
            "bname": self.bname, 
            "email": self.email, 
            "password": self.password, 
            "paypal": self.paypal, 
            "address": self.address,
            "bdescription": self.bdescription
        }


@app.route("/business")
def get_all():
    businesslist = Business.query.all()
    if len(businesslist):
        return jsonify(
            {
                "code": 200,
                "data": {
                    "business": [business.json() for business in businesslist]
                }
            }
        )
    return jsonify(
        {
            "code": 404,
            "message": "There are no businesses."
        }
    ), 404


@app.route("/business/<string:email>", methods=['GET'])
def find_existingby_bemail(email):
    # password = request.json.get('password', None)
    # email = request.json.get('email', None)
    business = db.session.query(Business).filter(Business.email == email).first()
    if business:
        result = business.json()
        del result['password']
        return jsonify(
            {
                "code": 200,
                "data": result
            }
        )
    return jsonify(
        {
            "code": 404,
            "message": "Business not found."
        }
    ), 404
    
@app.route("/check/business", methods=['POST'])
def find_by_bemail():
    password = request.json.get('password', None)
    email = request.json.get('email', None)
    business = db.session.query(Business).filter((Business.email == email) & (Business.password == password)).first()
    if business:
        result = business.json()
        del result['password']
        return jsonify(
            {
                "code": 200,
                "data": result
            }
        )
    return jsonify(
        {
            "code": 404,
            "message": "Business not found."
        }
    ), 404


@app.route("/business" ,methods=['POST'])
def create_business():
    bname = request.form.get('name', None)
    bdescription = request.form.get('description', None)
    paypal = request.form.get('paypal', None)
    address = request.form.get('address', None)
    email = request.form.get('email', None)
    password = request.form.get('password', None)
    businessinexist = db.session.query(Business).filter(Business.email == email).first()
    if businessinexist:
        return jsonify(
            {
                "code": 409,
                "message": "Account Exist.Please Sign in."
            }
        ), 409
        
    business = Business(bname=bname,email=email,address=address,bdescription=bdescription,paypal=paypal,password=password)

    try:
        db.session.add(business)
        db.session.commit()
    except Exception as e:
        return jsonify(
            {
                "code": 500,
                "message": "An error occurred while creating the business. " + str(e)
            }
        ), 500

    result = business.json()
    del result['password']
    return jsonify(
        {
            "code": 200,
            "data": result
        }
    ), 201

#@app.route("/business/<string:bid>", methods=['PUT'])
#def update_business(bid):
#    business = Business.query.filter_by(bid=bid).first()
#    if business:
#        data = request.get_json()
#        if data['bname']:
#            business.bname = data['bname']
#        if data['bdescription']:
#            business.bdescription = data['bdescription']
#        if data['email']:
#            businesss.email = data['email']
#        if data['paypal']:
#            business.paypal = data['paypal']
#        if data['password']:
#            business.password = data['password']
#        if data['address']:
#            business.address = data['address']
#        db.session.commit()
#        return jsonify(
#            {
#                "code": 200,
#                "data": business.json()
#            }
#        )
#    return jsonify(
#        {
#            "code": 404,
#            "data": {
#                "bid": bid
#            },
#            "message": "Business not found."
#        }
#    ), 404


@app.route("/business/<string:bid>", methods=['DELETE'])
def delete_business(bid):
    business = Business.query.filter_by(bid=bid).first()
    if business:
        db.session.delete(business)
        db.session.commit()
        return jsonify(
            {
                "code": 200,
                "data": {
                    "big": bid
                }
            }
        )
    return jsonify(
        {
            "code": 404,
            "data": {
                "bid": bid
            },
            "message": "Business not found."
        }
    ), 404


if __name__ == '__main__':
    app.run(port=5004, debug=True)