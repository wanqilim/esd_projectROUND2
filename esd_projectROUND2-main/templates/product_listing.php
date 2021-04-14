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
  <title>List Product</title>
  <script>
    if (!sessionStorage.getItem('loggedin')) {
      console.log('not logged in')
      window.location.href = 'Loginpage.php?msg=notloggedin';
    }
  </script>
</head>

<body>

  <?php include "nav.html" ?>


  <div class="container mx-auto text-center justify-content-center">
    <h1>Add a new product</h1>
    <?php if (isset($_GET['pid'])) { ?>
      <div class="alert alert-success" role="alert">
        Product Created! PID: <?php echo $_GET['pid'] ?>
      </div>
    <?php } ?>

    <form method="POST" id='theForm' enctype="multipart/form-data" name='fileinfo'>
      <div class="row justify-content-center">
        <div class="form-group">
          <label for="=PName">Product Name:</label>
          <input type="text" class="custom-file-input form-control" name='pname' id="Pname" placeholder=" Product Name">
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="form-group">
          <label for="exampleInputFile">Insert Image</label>
          <small id="fileHelp" class="form-text text-muted">Please upload an image of your product.</small>
          <input type='file' id='upload' name="file" onchange="readImg(this);" />
          <img id="pic" src="#" alt="" aria-describedby="fileHelp" />


        </div>
      </div>

      <div class="row justify-content-center">
        <label for="Price">Product Price: </label>

        <span class="input-group-text col-1" id="addon-wrapping" style="margin-left:10px">S$</span>
        <input type="text" class="col-3" placeholder="0.00" aria-label="Product Price" name="price" aria-describedby="addon-wrapping">
      </div>

      <div class="row justify-content-center">
        <div class="form-group">
          <label for="Description">Product Description:</label>
          <textarea class="form-control" id="Description" name="pdesc" placeholder="Write a short description about your product..." rows="3"></textarea>
        </div>
        <input type="hidden" name="bid" value="{{session['data']['bid']}}">

      </div>
      </br>
      <div class="row justify-content-center">
        <button type='submit' class="btn btn-warning col-2" id='add'>Add Product</button>
      </div>
    </form>
    <script>
      function readImg(input) {
        if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function(e) {
            $('#pic')
              .attr('src', e.target.result)
              .width(300)
              .height(300);
          };

          reader.readAsDataURL(input.files[0]);
        }
      }
      var form = document.forms.namedItem("fileinfo");

      form.addEventListener('submit', function(e) {
        e.preventDefault();
        var bid = sessionStorage.getItem('bid');
        product_url = "http://127.0.0.1:5001/add/product";
        console.log('before');
        processaddingproducts(product_url);

        console.log('done');
      });

      async function processaddingproducts(url) {
        var formData = new FormData(form);
        formData.append("imgfile", document.getElementById('upload').files[0]);
        const response = await fetch(url, {
          body: formData,
          method: "POST",
          mode: 'cors', // no-cors, *cors, same-origin
          cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
          credentials: 'same-origin', // include, *same-origin, omit
          origin: ["http://localhost:8080", "http://localhost:5001"],
          redirect: 'follow' // manual, *follow, error
        });
        console.log('call');
        if (response.status >= 200 && response.status < 300) {

          console.log('response ok');
          data = await response.json();
          console.log(data);
            productinfo = data.data;
            window.location.href = 'product_listing.php?pid=' + productinfo['pid'];
          
        } else {
          alert("There has been an error in listing the product, please refresh and try again");
        }

      }
    </script>
  </div>
  <?php include "footer.html" ?>


</body>

</html>