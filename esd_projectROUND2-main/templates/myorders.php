<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.html" ?>

    <title>My Orders</title>
    <script>
        if (!sessionStorage.getItem('loggedin')) {
            console.log('not logged in')
            window.location.href = 'Loginpage.php?msg=notloggedin';
        }
    </script>
</head>

<body>
    <?php include "nav.html" ?>

    <section id="Header" class="py-2">
        <div class="text-center">

        </div>
    </section>

    <!-- Implementing search bar with Vue -->
    <div id="app">
        <div class='container'>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">

                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 text-center">
                    <h1>My Orders</h1>
                </div>
                <div class="container" id="app">
                    <div class="text-center" v-if="!ordersExist && loadOrders">
                        <h2>Loading in progress...</h2>
                        <img src='https://i.gifer.com/4V0b.gif'>
                    </div>
                    <ul class="list-group" v-if='ordersExist && !loadOrders'>
                        <div class="justify-content-center" v-for='(orderlist,i) in orders' style="background-color: #F0AEA0;border-radius: 20px;">
                            <h2 class="text-center">Group Order #{{ i }} </h2>
                            <input type="hidden" v-if='isbusiness && checkFulfillment[i]' id="group_oid" name="group_oid" v-bind:value="i" />
                            <button id='submit' v-if='isbusiness && checkFulfillment[i]' v-on:click='fulfillOrder(i,orderlist)' class="btn btn-outline-danger">Fulfil Order</button>
                            <li class="list-group-item" v-for='(order,j) in orderlist'>
                                <div class="row">
                                    <img class="col-3 img-fluid" :src="'../static/images/'+order.imgname" style="border-top-left-radius: 20px;border-top-right-radius: 20px">
                                    <div class="col-3">
                                        <h3>{{order.pname}}</h3>

                                        <p>{{order.address}} </p>
                                        <h5 id='statusline'>{{order.dStatus}} </h5>
                                    </div>
                                    <div class="col-2"></div>
                                    <div class="col-3">
                                        <p>{{order.datetime}}</p>
                                        <h4>Quantity: {{order.quantity}}</h4>

                                    </div>
                                </div>
                            </li>
                        </div>
                        <!-- end for -->
                    </ul>
                    <!-- else -->
                    <div v-if="!ordersExist && !loadOrders">
                        <h1>No Orders!</h1>
                        <h2>{{ noOrdermsg1 }}</h2>
                        <p><a :href="noOrderlink">{{ noOrdermsg2}}</a></p>
                    </div>
                    <!-- end if -->

                </div>

            </div>
        </div>
    </div>

    <script type="text/javascript">
        console.log('getallorders');
        var business_url = "http://127.0.0.1:5100/check_order_biz";
        var customer_url = "http://127.0.0.1:5300/check_order_cust";
        var acctType = sessionStorage.getItem('acctType');

        if (acctType == 'business') {
            bid = sessionStorage.getItem('bid');
            getURL = business_url + '/' + bid;
        } else if (acctType == 'customer') {
            cid = sessionStorage.getItem('cid');
            getURL = customer_url + '/' + cid;
        }
        console.log(acctType, getURL);

        var app = new Vue({
            el: "#app",
            computed: {
                hasOrders: function() {
                    return this.books.length > 0;
                }
            },
            data: {
                orders: {},
                message: "There is a problem retrieving books data, please try again later.",
                ordersExist: false,
                loadOrders: true,
                noOrdermsg1: '',
                noOrdermsg2: '',
                noOrderlink: '',
                isbusiness: '',
                checkFulfillment: {}

            },
            created: async function() {

                axios.get(getURL)
                    .then((response) => {
                        if (acctType == 'business') {
                            this.noOrdermsg1 = 'There are no orders.'
                            this.noOrdermsg2 = ''
                            this.isbusiness = true
                        } else {
                            this.noOrdermsg1 = 'You have not order anything.'
                            this.noOrdermsg2 = 'Shop here now'
                            this.noOrderlink = 'marketplace.php'
                            this.isbusiness = false
                        }
                        data = response.data;
                        console.log(data);
                        if (data.code === 404) {
                            console.log('404');
                            this.message = data.message
                            this.ordersExist = false
                            this.loadOrders = false
                        } else {
                            // add codes
                            console.log('not 404');
                            if (data.data.required_info.length > 0) {
                                this.ordersExist = true
                                this.loadOrders = false
                                newdict = {};
                                for (info of data.data.required_info) {
                                    if (info.group_oid in newdict) {
                                        newdict[info.group_oid].push(info);
                                    } else {
                                        newdict[info.group_oid] = [info];
                                    }
                                    if (info.dStatus == 'Unfulfilled') {
                                        this.checkFulfillment[info.group_oid] = true;
                                    } else {
                                        this.checkFulfillment[info.group_oid] = false;
                                    }


                                }
                                console.log(newdict);
                                this.orders = newdict;

                            } else {
                                this.ordersExist = false
                                this.loadOrders = false
                            }

                        }
                    })
                    .catch(error => {
                        // Errors when calling the service; such as network error, 
                        // service offline, etc
                        // console.log(this.message + error);
                        console.log(error)
                    });

            },
            methods: {
                fulfillOrder(group_oid,orderlist) {
                    console.log(group_oid);
                    axios.put('http://127.0.0.1:5400/fulfill_order', {
                            'group_oid': group_oid,
                            'dStatus': 'Fulfilled'
                        })
                        .then((response) => {
                            console.log(response);
                            console.log(orderlist);
                            for (order of orderlist){
                                order.dStatus = 'Fulfilled'
                                this.checkFulfillment[order.group_oid] = false
                            }
                        })
                        .catch(error => {
                            // Errors when calling the service; such as network error, 
                            // service offline, etc
                            // console.log(this.message + error);
                            console.log(error)
                        });
                }
            }

        }); //end of app
    </script>



    <?php include "footer.html" ?>



</body>

</html>