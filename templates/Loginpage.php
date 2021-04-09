<!doctype html>
<html lang="en">

<head>
    <?php include "head.html" ?>
    <style>
        .box {
            width: 400px;
            height: auto;
        }

        .form-group {
            width: 400px;
        }

        .center {
            margin-left: 350px;
        }
    </style>
    <title>Login Page</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha512/0.8.0/sha512.min.js"></script>

</head>

<body>
    <?php include "nav.html" ?>
    <script>
        console.log(window.sessionStorage);
    </script>
    <div class="container">
        <h1>Log In</h1>
        <br>

        <div class="row mx-auto">

            <div>

                <div class="form-group">
                    <label for="email">Email address:</label>
                    <input type="email" class="form-control" name='email' id="email" placeholder="Email">
                </div>
            </div>

            <br>

            <div>
                <div class="form-group">
                    <label for="pwd">Password:</label>
                    <input type="password" name='password' class="form-control" id="pwd" placeholder="Password">
                </div>
            </div>

            <br>

            <div class=form-group>
                <h6>Select Account Type:</h6>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="acctType" id="acctType" value="business">
                <label class="form-check-label" for="acctType">Business Account</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="acctType" id="acctType2" value="customer">
                <label class="form-check-label" for="acctType">Member Account</label>
            </div>

            <div>
                <div style="margin-left: 130px; margin-top:20px;">
                    <button onclick="login()" class="btn btn-success" id='loginbtn' disabled>Log in</button>
                </div>
                <script>
                    $('input[type=radio][name=acctType]').change(function() {

                        if ($("input[type='radio'][name='acctType']:checked").val) {
                            var acctType = $("input[type='radio'][name='acctType']:checked").val();
                            $('#loginbtn').prop('disabled', false);
                        } else {

                            $('#loginbtn').prop('disabled', true);
                        }
                    });

                    function login() {
                        email = document.getElementById('email').value;
                        password = sha512(document.getElementById('pwd').value);
                        accountType = document.querySelector("input[name=acctType]:checked").value
                        console.log(email, password, accountType)
                        if (accountType == 'customer') {
                            login_url = "http://127.0.0.1:5003/check/customer/";
                        } else if (accountType == 'business') {
                            login_url = "http://127.0.0.1:5004/check/business";
                        }
                        retrievelogin(login_url,accountType, {
                            'email': email,
                            'password': password
                        });

                        console.log(window.sessionStorage);
                    }
                    async function retrievelogin(url,accountType, data) {
                        try {
                            const response = await fetch(url, {
                                method: 'POST', // *GET, POST, PUT, DELETE, etc.
                                mode: 'cors', // no-cors, *cors, same-origin
                                cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
                                credentials: 'same-origin', // include, *same-origin, omit
                                headers: {
                                    'Content-Type': 'application/json'
                                    // 'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                origin: ["http://localhost:8080", "http://localhost:5003","http://localhost:5004"],
                                redirect: 'follow', // manual, *follow, error
                                body: JSON.stringify(data) // body data type must match "Content-Type" header
                            });
                            if (!response.ok) {
                                alert("There has been an error in logging in, please refresh and try again");
                            } else {
                                data = await response.json();
                                console.log(data);
                                if (data.code == 200){
                                    sessionStorage.clear();
                                    console.log(window.sessionStorage);
                                    if (data.code == 200){
                                        userinfo = data.data;
                                        sessionStorage.setItem('loggedin',true);
                                        sessionStorage.setItem('acctType',accountType);
                                        for (key in userinfo){
                                            sessionStorage.setItem(key,userinfo[key]);
                                            window.location.href ='marketplace.php?msg=loggedin';
                                        }
                                    }
                                }
                            }
                        } catch (error) {

                            alert(error)
                        }
                    }
                </script>
            </div>
        </div>

        <?php include "footer.html" ?>

</body>

</html>