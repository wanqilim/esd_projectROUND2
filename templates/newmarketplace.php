<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.html" ?>
    <title>Marketplace</title>

    <style>
        .card:hover {
            opacity: 1;
            background: #F8F4FF;
            color: #800000;
        }
    </style>

    <!-- Paypal API -->
    <script src="https://www.paypal.com/sdk/js?client-id=AVL3t_B9WIEb-CdLDHYK1sW8nXEtp7GM8Qk9m29QTvb-OYyb2dxdo_PapsHFT5KI08OyWiWDNtuL9tI0&currency=SGD">
        // Required. Replace YOUR_CLIENT_ID with your sandbox client ID.
    </script>

</head>

<body>
    <?php include "nav.html" ?>

    <section id="Header" class="pb-2">
        <div id="banner" class="container">
            <img src="../static/images/Homies Label.png" class="img-fluid">
        </div>
    </section>

    <section id="market">
        <div class='container'>
            <?php if (isset($_GET['msg'])) { ?>
                <?php if ($_GET['msg'] == 'loggedin') { ?>
                    <div class="alert alert-success" role="alert">
                        Welcome back
                    </div>
                <?php } else if ($_GET['msg'] == 'congratulations') { ?>
                    <div class="alert alert-success" role="alert">
                        Congratulations ! You are registered!
                    </div>
                <?php } ?>
            <?php } ?>
            <div class="row">
                <div class="col-12 text-center">
                    <h1>Marketplace</h1>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row" id="marketSpace">

            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-4"></div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <h3 class="text-center">Add things into your cart!</h3>
                    <table class="table table-hover" id="cartDisplay">
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Price/Unit</th>
                        </tr>
                    </table>
                </div>
                <div class="col-4"></div>
            </div>
        </div>

        <div id="paymentContainer" class="container text-center">
            <div style='text-align:center;' id="paypal-button-container"></div>
        </div>

    </section>

    <script>
        getProducts()

        // Check login status
        console.log(window.sessionStorage);

        var status = sessionStorage.getItem('loggedin') // Return True or False
        var user = sessionStorage.getItem('acctType') // Return Str = business or customer

        if (status && user == "customer") {
            document.getElementById("paymentContainer").style.display = "block"
            var cartButtons = document.getElementsByClassName("addToCart")
            for (button of cartButtons) {
                button.style.display = "block";
            }
            document.getElementById('cartDisplay').style.display = "block"
        } else {
            document.getElementById("paymentContainer").style.display = "none"
            document.getElementById('cartDisplay').style.display = "none"

        }
        // End login status check


        var myCart = [];

        function getData() {

            let cart = [];
            // This should retrieve an JSON object of the item selected
            let itemSelected = document.getElementsByName("product");
            let cartDisplay = document.getElementById("cartDisplay")

            // Check if customer is logged in, if not not allowed to add item to cart
            if (!status || user != "customer") {
                for (item of itemSelected) {
                    if (item.checked == true) {
                        alert("Please login as a customer add items to your cart")
                    }
                }
            } else {
                cartDisplay.innerHTML = ""

                for (item of itemSelected) {
                    if (item.checked == true) {
                        // change color and label of checked item

                        let label = item.previousElementSibling;
                        let parent = item.parentElement;
                        parent.className = "btn btn-danger";
                        label.innerText = 'Remove from Cart';


                        // Retrieve list of prod info in the following sequence [pid, pname, price, stock]

                        let checkedItem = item.value
                        // console.log(item.value)

                        checkedItem = checkedItem.split(',')

                        // Check if selected item has stock of > 0

                        cart.push(checkedItem)

                        cartDisplay.innerHTML += `
                            <tr>
                            <td>${checkedItem[0]}</td>
                            <td>${checkedItem[1]}</td>
                            <td>$${checkedItem[2]}</td>
                            </tr>`


                    } else if (item in cart && item.checked == false) {
                        console.log('else if')
                        let label = item.previousElementSibling;
                        let parent = item.parentElement;
                        parent.className = "btn btn-success";
                        label.innerText = 'Add to Cart';
                        for ([i, element] of cart) {
                            cart.pop(i)
                        }
                    } else {
                        //console.log('else');
                        let label = item.previousElementSibling;
                        let parent = item.parentElement;
                        parent.className = "btn btn-success";
                        label.innerText = 'Add to Cart';
                    }
                }
                // This checks if cart is being dynamically updated
                // console.log(cart)
                myCart = cart;
                console.log(myCart)
            }


        }

        async function getProducts() {
            try {
                var product_url = "http://127.0.0.1:5001/product"
                const response = await fetch(product_url);
                if (!response.ok) {
                    alert("There has been an error in retrieving products, please refresh and try again");
                } else {
                    const data = await response.json();
                    //console.log(data);
                    if (data.code == 200) {
                        let marketSpace = document.getElementById("marketSpace")

                        let prodArr = data.data.products;
                        //console.log(prodArr);

                        for (item of prodArr) {
                            let disabled = "disabled"

                            if (parseInt(item.stock) > 0) {
                                disabled = ""
                            }
                            marketSpace.innerHTML += `
                            <div class="col-lg-3 col-md-2 mb-2 d-flex justify-content-center text-center w3-animate-opacity">
                                <div class="card w3-hover-shadow " style="width: 20rem; border-radius: 20px;">
                                    <img src="../static/images/${item.imgname}" class="card-img-top"
                                        style="border-top-left-radius: 20px;border-top-right-radius: 20px">
                                    <div class="card-body">
                                        <h5 class="card-title">${item.pname}</h5>
                                        <p class="card-text">${item.pdescription}</p>
                                        <p class="card-text" style="font-weight: bold">SGD$${item.price.toFixed(2)}</p>
                                        <p class="card-text" style="font-weight: bold">Stock: &nbsp ${item.stock}</p>
                                        <div class="addToCart btn-group btn-group-toggle" data-toggle="buttons">
                                        <div class="btn btn-success">
                                        <label >Add to Cart</label>
                                        <input type="checkbox" ${disabled} name="product" value="${[item.pid, item.pname, item.price, item.stock]}" onclick="getData()" style='opacity:0.3'>
                                        </div>
                                        </div>
                                
                                        
                                        <br> <br>
                                </div>
                            </div>`


                        }

                    }
                }
            } catch (error) {

                alert(error)
            }

        };

        var PAYPAL_CLIENT = 'AVL3t_B9WIEb-CdLDHYK1sW8nXEtp7GM8Qk9m29QTvb-OYyb2dxdo_PapsHFT5KI08OyWiWDNtuL9tI0';
        var PAYPAL_SECRET = 'EF1n5ItUXHKwB8qI7HRGzLtN9M5U9omdMKYTRm0BSvUkRgCr5LLyQvThBN4OJMl3a09yGFU8oXVf8Jpb';
        var PAYPAL_ORDER_API = 'https://api-m.paypal.com/v2/checkout/orders/';


        paypal.Buttons({
            createOrder: function(data, actions) {
                var myCheckOutArr = myCart

                var thePrice = 0

                for (item of myCheckOutArr) {
                    thePrice += parseFloat(item[2])
                }

                thePrice = parseFloat(thePrice).toFixed(2)

                return actions.order.create({
                    purchase_units: [{
                        description: "New Order",
                        amount: {
                            value: thePrice // change to order price
                        }
                    }] //end of purchase units
                }); //end of return
            },
            onApprove: function(data, actions) {
                // This function captures the funds from the transaction.
                return actions.order.capture().then(function(details) {
                    // This function shows a transaction success message to your buyer.
                    alert('Transaction completed by ' + details.payer.name.given_name);
                    console.log(details)

                    // Pass order info over to order microservice after transaction approves
                    var placeOrderUrl = "http://127.0.0.1:5200/place_order"
                    addNewOrder(placeOrderUrl);

                }); //end of return

            } // end of onApprove

            // render in the paypal button container
        }).render('#paypal-button-container');

        

        async function addNewOrder(url) {
            // Retrieve myCart, which consists of the following data, each arr is one new prod [[pid, pname, price, stock]]
            var myCheckOutArr = myCart

            var pidArr = [] // [1,3,4]

            // Add each PID of the order upon checkout to send over to order MS
            for (item of myCheckOutArr) {
                pidArr.push(parseInt(item[0])) 
            }

            var pidObj = {}

            pidObj["newOrder"] = pidArr

            var cid = sessionStorage.getItem('cid')

            pidObj["cid"] = cid

            pidJSON = JSON.stringify(pidObj)

            // console.log(pidJSON)

            // console.log(typeof(pidJSON))

            let xhr = new XMLHttpRequest()
            let my_url = url

            xhr.open("POST", my_url, true);

            xhr.setRequestHeader("Content-Type", "application/json");

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {

                    var myResponse = JSON.parse(this.responseText)
                    console.log(myResponse.data)
                    console.log(myResponse.data.datetime)
                    alert("Order has been successfully created on " + myResponse.data.datetime)
                    
                    window.location.href = "newmarketplace.php?msg=loggedin"

                }
            }

            xhr.send(pidJSON)



        }
    </script>

    <?php include "footer.html" ?>

</body>


</html>