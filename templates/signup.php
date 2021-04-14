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
    </style>
    <title>Registation Form</title>
</head>

<body>
    <?php include "nav.html" ?>

    <div class="container">
        <h1> Register an account with us </h1>
        <br>

        <form method="POST" id='signupform' name="signupform">

            <div class="row">
                <div class=form-group>
                    <h6>Select Account Type:</h6>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="acctType" id="acctType" value="business">
                        <label class="form-check-label" for="acctType">Business Account</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="acctType" id="acctType2" value="customer">
                        <label class="form-check-label" for="acctType">Member Account</label>
                    </div>

                </div>
            </div>

            <br>

            <div class="row">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                </div>
            </div>
            <br>

            <div class="row">
                <div class="form-group">
                    <label for="email">Email address:</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                </div>
                <div class="form-group">
                    <label for="confirmemail">Confirm Email address:</label>
                    <input type="email" class="form-control" id="confirmemail" name="confirmemail" placeholder="Confirm Your Email address">
                </div>
            </div>

            <br>

            <div class="row">
                <div class="form-group">
                    <label for="pwd">Password:</label>
                    <input type="password" class="form-control pwd" id="pwd" class="pwd" name="password" placeholder="Password">
                </div>

                <div class="form-group">
                    <label for="pwd">Confirm Password:</label>
                    <input type="password" class="form-control pwd" id="cpwd" class="pwd" placeholder="Confirm Password">
                </div>
            </div>

            <br>

            <div class="row">
                <div class="form-group">
                    <label for="email">Paypal account Email address:</label>
                    <input type="email" class="form-control" id="paypal" class="paypal" name="paypal" placeholder="Paypal Email">
                </div>

            </div>

            <br>

            <div class="row">
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea class="form-control" id="address" name="address" placeholder="Address" rows="3"></textarea>
                </div>


                <div class="form-group" style="padding-left:30px" id='descsection'>
                    <label for="description">Description:</label>
                    <textarea class="form-control" id="description" name="description" placeholder="Tell us more about your business..." rows="3"></textarea>
                </div>
            </div>

            <br>

            <div class="center">
                <button type="submit" class="btn btn-primary btn-lg" id='registerbtn' disabled>Register</button>
            </div>
            <div id='msg'></div>
            <script>
                $('#descsection').hide();
                $('input[type=radio][name=acctType]').change(function() {

                    if ($("input[type='radio'][name='acctType']:checked").val()) {
                        var acctType = $("input[type='radio'][name='acctType']:checked").val();

                        $('#registerbtn').prop('disabled', false);

                        if ($("input[type='radio'][name='acctType']:checked").val() == 'business') {
                            $('#descsection').show();
                        } else {
                            $('#descsection').hide();
                        }
                    } else {
                        $('#registerbtn').prop('disabled', true);
                    }
                });
                var form = document.forms.namedItem("signupform");

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var email = document.getElementById('email').value;
                    var confirmemail = document.getElementById('confirmemail').value;
                    var password = sha512(document.getElementById('pwd').value);
                    var confirmpassword = sha512(document.getElementById('cpwd').value);
                    accountType = document.querySelector("input[name=acctType]:checked").value;

                    if (accountType == 'customer') {
                        signup_url = "http://127.0.0.1:5003/customer";
                        origin_url = "http://localhost:5003";
                    } else {
                        signup_url = "http://127.0.0.1:5004/business";
                        origin_url = "http://localhost:5004";
                    }
                    addAccount(signup_url, origin_url);

                });


                async function addAccount(url, originurl) {
                    var formData = new FormData(form);
                    const response = await fetch(url, {
                        body: formData,
                        method: "POST",
                        mode: 'cors', // no-cors, *cors, same-origin
                        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
                        credentials: 'same-origin', // include, *same-origin, omit
                        origin: ["http://localhost:8080", originurl],
                        redirect: 'follow' // manual, *follow, error
                    });
                    console.log('call');
                    if (response.status >= 200 && response.status < 300) {
                        console.log('response ok');
                        data = await response.json();
                        console.log(data);
                        if (response.status >= 200 && response.status < 300) {
                            sessionStorage.clear();
                            userinfo = data.data;
                            sessionStorage.setItem('loggedin', true);
                            sessionStorage.setItem('acctType', accountType);
                            for (key in userinfo) {
                                sessionStorage.setItem(key, userinfo[key]);
                            }
                            console.log(window.sessionStorage);

                            if (accountType == 'customer') {
                                window.location.href = 'newmarketplace.php?msg=congratulations';
                            } else {
                                window.location.href = 'myorders.php?msg=congratulations';
                            }

                        } else {

                        }
                    } else {
                        alert("There has been an error in registering your account, please refresh and try again");
                    }

                }
            </script>
        </form>
    </div>

    <?php include "footer.html" ?>

</body>

</html>