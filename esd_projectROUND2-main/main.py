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
from werkzeug.utils import secure_filename

import time

app = Flask(__name__)
app.config["DEBUG"] = True
CORS(app)
app.secret_key = 'esdT6thebest'


cuslogin_URL = environ.get('cuslogin_URL') or "http://localhost:5003/customer"
bizlogin_URL = environ.get('bizlogin_URL') or "http://localhost:5004/"
product_URL = environ.get('product_URL') or "http://localhost:5001/product"
checkorder_biz_URL = environ.get('checkorder_biz_URL') or "http://localhost:5100/check_order_biz/"
checkorder_cust_URL = environ.get('checkorder_cust_URL') or "http://localhost:5300/check_order_cust/"

uploads_dir = os.path.join( 'static\\images')
# os.makedirs(uploads_dir)

@app.route("/")
def home():
    msg = ''
    if 'acctType' in session:
        if session['acctType'] == 'customer':
            return render_template("marketplace.php", user=session['data']['cname'])
        else:
            return render_template("marketplace.php", user=session['data']['bname'])
    return render_template("marketplace.php", msg=msg)


@app.route("/signup")
def show_signuppage():
    return render_template('signup.html')

@app.route("/myorders")
def show_existingorders():
    # tb adding code to verify login
    print(session)
    
    if 'acctType' in session:
        if session['acctType'] == 'customer':
            result = invoke_http(
                checkorder_cust_URL+'/'+str(session['data']['cid']), method='GET')
            return render_template('myorders.html',data=[result['data']])
        
        elif  session['acctType'] == 'business':
            result = invoke_http(
                checkorder_biz_URL+'/'+str(session['data']['bid']), method='GET')
            return render_template('myorders.html',data=[result['data']])
        
    return redirect(url_for('.login',msg='Please log in'))

@app.route("/payment")
def show_payment():
    # tb adding code to verify login
    return render_template('payment-example.html', user=session['data']['email'])


@app.route('/create/product', methods=['GET', 'POST'])
def upload():
    if session.get('loggedin') == True:    
        if session.get('acctType') == 'business':
            if request.method == 'POST':
                # save the  file
                img = request.files['imgfile']
                fileext = img.filename.split('.')[-1]
                
                timestr = time.strftime("%Y%m%d-%H%M%S")
                newfilename = str(timestr)+'.'+fileext 
                img.save(os.path.join(uploads_dir, secure_filename(newfilename)))
                
                sendjson = {
                    "pname": request.form['pname'], 
                    "price": request.form['price'], 
                    "pdescription": request.form['pdesc'],
                    "imgname": newfilename,
                    "bid": session['data']['bid']
                }
                result = invoke_http(
                        product_URL, method='POST', json=sendjson)
                
                print(result)
                return render_template('product_listing.html',pid=result['data']['pid'])
            # for normal get to create product listing page
            return render_template('product_listing.html',msg='')
        # if not business
        return redirect(url_for('.home',msg='no such url'))
    # if not logged in
    return redirect(url_for('.login',msg='Please log in'))


@app.route("/login")
def login():
    msg = ''
    return render_template('Loginpage.html', msg=msg)


@app.route("/login", methods=['POST'])
def configure_login():
    msg = ''
    account_type = request.form['acctType']
    print(request.form)
    if request.method == 'POST' and 'email' in request.form and 'password' in request.form:
        
        email = request.form['email']
        password = hashlib.md5(
            request.form['password'].encode('utf-8')).hexdigest()

        if (account_type == "customer"):
            login_result = invoke_http(
                cuslogin_URL+'/'+email, method='POST', json={"email": email, "password": password})
        elif(account_type == "business"):
            login_result = invoke_http(
                bizlogin_URL+'check/business', method='POST', json={"email": email, "password": password})
        else:
            msg = 'Please select a account type!'
            
            return redirect(url_for('.login',msg =msg))

        if login_result['code'] == 200:
            # Create session data, we can access this data in other routes
            session['loggedin'] = True
            session['acctType'] = account_type
            session['data'] = login_result['data']
            

            # Redirect to home page
            msg = 'Logged in successfully!'
            
            return redirect(url_for('.home',msg =msg))
            

        msg = 'Incorrect username/password!'
        return redirect(url_for('.login',msg =msg))
    
    
@app.route("/signup", methods=['POST'])
def configure_signup():
    msg = ''
    account_type = request.form['acctType']
    print(request.form, file=sys.stderr)
    if  'email' in request.form :
        email = request.form['email']
        
        password=request.form['password']
        if (account_type == "customer"):
            result = invoke_http(
                cuslogin_URL+'/'+email, method='GET', json={"email": email})
            desc = ''
        elif(account_type == "business"):
            result = invoke_http(
                bizlogin_URL+'/business/'+email, method='GET', json={"email": email})
            desc = request.form['description']

        if result['code'] != 200:
            # Redirect to home page
            password = hashlib.md5(password.encode('utf-8')).hexdigest()
            mydict = {
                'name':request.form['name'],
                'email':email,
                'password':password,
                'paypal':request.form['paypalemail'],
                'address':request.form['address'],
                'description': desc,
            }
            app.logger.info(mydict)
            
            if (account_type == "customer"):
                registerresult = invoke_http(cuslogin_URL, method='POST', json=mydict)
            elif(account_type == "business"):
                registerresult = invoke_http(bizlogin_URL+'/business', method='POST', json=mydict)
            
            if registerresult['code'] == 200:
                app.logger.info(registerresult)
                return  redirect(url_for('.login',msg ='Signup sucessfully Please login'))
            else:
                return  redirect(url_for('.home',msg ='Signup is not sucessfull. Please try again later'))
        else:
            return  redirect(url_for('.home',msg ='Customer exists'))
        


@app.route('/logout')
def logout():
    # Remove session data, this will log the user out
    session.pop('loggedin', None)
    session.pop('acctType', None)
    session.pop('data', None)
    # Redirect to login page
    return redirect(url_for('login'))


if __name__ == "__main__":
    app.run(host="0.0.0.0", port="5000")