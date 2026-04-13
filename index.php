<?php
$pageTitle = "Login";
include "header.php"; //loads header file

session_start();

$error = ""; //stores error messages

if ($_SERVER["REQUEST_METHOD"] === "POST") { //checks if the suer submitted the login form
    $emailaddress = $_POST["emailaddress"]; //retrieves the values the user entered in the form
    $password = $_POST["password"]; //and stores them as variables

    // simple test login
    if ($emailaddress === "admin" && $password === "password") {
        $_SESSION["user_email"] = $emailaddress; //if the email posted matches the email stored
        header("Location: dashboard.php"); //direct to  the dashboard
        exit();
    } else {
        $error = "Invalid emailaddress or password";
    }
}
?>

<body>
  <div class="container">
    <div class="row justify-content-center mt-5">
      

      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-body">
            <h3 class="text-center mb-4">Login</h3>

            <img src="/files/logo.png" style="height:300px" alt="Logo" class="img-fluid mx-auto d-block mb-4">            
            
            <div class="text-center">
              Enter your details to login to Striver, your companion habit and task tracker.<br>             
            </div>
            
          </div>
        </div>
      </div>
      
      
      
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-body">

            <?php if ($error): ?>
            <div class="alert alert-danger">
              <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="POST">
              <div class="mb-3">
                <input type="text" name="emailaddress" id="emailaddress" class="form-control" placeholder="Email Address" required>
              </div>
              <div class="mb-3">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
              </div>
              <div class="d-grid">
                <button class="btn btn-primary">Login</button>
              </div>
                       
              <div class="d-grid mt-4 mb-4 text-center">
                <a href="newpassword.php">Forgotten Password</a>
              </div>

              <div class="d-grid">
                <a href="/account/newaccount.php" class="btn btn-secondary">Create new account</a>
              </div>

            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</body>

<?php include "footer.php"; ?>
