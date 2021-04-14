<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.html" ?>
    <title>Marketplace</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AVL3t_B9WIEb-CdLDHYK1sW8nXEtp7GM8Qk9m29QTvb-OYyb2dxdo_PapsHFT5KI08OyWiWDNtuL9tI0&currency=SGD">
        // Required. Replace YOUR_CLIENT_ID with your sandbox client ID.
    </script>
</head>

<body>

    <?php include "nav.html" ?>
    <script>
        console.log(window.sessionStorage);
    </script>



    <!-- Implementing search bar with Vue -->
    <div id="app">
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
                    <h1>Homie Marketplace</h1>
                </div>
            </div>
        </div>

        <div class="container">

            <div class="container">
                <div class="row" id="marketSpace">

                </div>
            </div>

            <div id="paymentContainer" class="container text-center">
                <span style="font-size:150%" id="Quantity">Input quantity (Keep the value to less than 11): &nbsp;
                </span><input onchange="getQuantity()" id="prodQ" style="width: 4em; text-align:center;" type="number" v-model="selectedQuantity" @change="checkAmt">
                <br> <br>
                
                <div style='text-align:center;' id="paypal-button-container"></div>
            </div>
        </div>
    </div>

    </div>

    <script>
        let user = `{{user}}`

        var app = new Vue({
            el: "#app",
            data: {
                selectedQuantity: 1,
                lower: 1,
                upper: 10,
            },
            delimiters: ["((", "))"],
            methods: {
                checkAmt: function() {
                    let amt = this.selectedQuantity
                    if (amt < 1) {
                        alert("Please have a quantity of more than or equal to 1");
                        this.selectedQuantity = this.lower;
                    } else if (amt > 10) {
                        alert("Please have a quantity of less than or equal to 10");
                        this.selectedQuantity = this.upper;
                    }
                },
            }
        });

        function getQuantity() {
            let prodQ = document.getElementById("prodQ").value
            //console.log(prodQ)
            return prodQ
        }

        // Get quantity of products 
        let theItem = "";

        let thePrice = "";

        let thePName = "";

        function getData() {
            // This should retrieve an JSON object of the item selected
            itemSelected = document.getElementsByName("product");

            for (item of itemSelected) {
                if (item.checked == true) {
                    // This should retrive pid of the selected item
                    //console.log(item.item)
                    theItem = item.value
                }
            }
            let pid_url = "http://127.0.0.1:5001/product/" + theItem;

            retrieveByPid(pid_url);

            //console.log(theItem)
        }

        async function retrieveByPid(query) {
            try {
                const response = await fetch(query);
                if (!response.ok) {
                    alert("There has been an error in retrieving products, please refresh and try again");
                } else {
                    const data = await response.json();
                    //console.log(data);
                    if (data.code == 200) {

                        let productArr = data.data;
                        //console.log(productArr);
                        thePrice = parseFloat(productArr.price).toFixed(2)
                        theDescrip = productArr.pname

                        //console.log(thePrice)
                        //console.log(theDescrip)
                    }
                }
            } catch (error) {

                alert(error)
            }
        }


        var PAYPAL_CLIENT = 'AVL3t_B9WIEb-CdLDHYK1sW8nXEtp7GM8Qk9m29QTvb-OYyb2dxdo_PapsHFT5KI08OyWiWDNtuL9tI0';
        var PAYPAL_SECRET = 'EF1n5ItUXHKwB8qI7HRGzLtN9M5U9omdMKYTRm0BSvUkRgCr5LLyQvThBN4OJMl3a09yGFU8oXVf8Jpb';
        var PAYPAL_ORDER_API = 'https://api-m.paypal.com/v2/checkout/orders/';



        paypal.Buttons({
            createOrder: function(data, actions) {
                var new_q = getQuantity()
                return actions.order.create({
                    purchase_units: [{
                        description: thePName,
                        amount: {
                            value: thePrice * parseInt(new_q) // change to order price
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
                    // window.location.href = "{{url_for('home')}}";
                    //console.log(user)
                    /*send_Request();
                    async function send_Request() {
                        let placeOrder_url = "http://127.0.0.1:5200/place_order"
                        new_q = getQuantity();
                        try {
                            const response = await fetch((placeOrder_url), {
                                method: 'POST',
                                headers: {
                                    "Content-type": "application/json"
                                },
                                body: JSON.stringify([new_q, theItem, user, details])
                            });
                            //console.log(response)
                            if (!response.ok) {
                                //console.log("Error when returning.")
                                alert("Problem uploading data onto Order")
                            } else {
                                const data = await response.json();
                                //console.log(data);
                                if (data.code == 200) {
                                    window.location.replace("myorders.html");
                                } else {
                                    
                                }
                            }
                        } catch (error) {
                            alert(error)
                        }

                    }*/
                }); //end of return

            } // end of onApprove

            // render in the paypal button container
        }).render('#paypal-button-container');

        var product_url = "http://127.0.0.1:5001/product"

        async function getProducts() {
            try {
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

                            marketSpace.innerHTML += `
                            <div class="col-lg-4 col-md-6 mb-3 d-flex justify-content-center text-center">
                                <div class="card w3-hover-shadow" style="width: 20rem; border-radius: 20px;">
                                    <img src="../static/images/${item.imgname}" class="card-img-top"
                                        style="border-top-left-radius: 20px;border-top-right-radius: 20px">
                                    <div class="card-body">
                                        <h5 class="card-title">${item.pname}</h5>
                                        <p class="card-text">${item.pdescription}</p>
                                        <p class="card-text">SGD$${item.price.toFixed(2)}</p>
                                        Select me to purchase <input type="radio" name="product" value="${item.pid}" onclick="getData()">
                                        
                                        <br> <br>
                                    </div>
                                </div>
                            </div>`
                        }

                    }
                }
            } catch (error) {

                alert(error)
            }

        }

        getProducts()
    </script>


    <?php include "footer.html" ?>



</body>

</html>