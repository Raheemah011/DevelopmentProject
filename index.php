<?php
$pageTitle = "Login";
include "header.php"; //loads header file


$error = ""; //stores error messages

if ($_SERVER["REQUEST_METHOD"] === "POST") { //checks if the suer submitted the login form
    $emailaddress = $_POST["emailaddress"]; //retrieves the values the user entered in the form
    $password = $_POST["password"]; //and stores them as variables

    if (empty($error) && empty(trim($emailaddress))) { //trim - removes blank spaces
        $error = "Email is required"; //if the user hasnt entered info in the email field an error message is returned
    }

    //now check all values are valid - check the email address using regex, pattern matches to see if it matches the correct email format      
    if (empty($error) && !preg_match('/^[\w\.\-]+@([\w\-]+\.)+[\w\-]{2,4}$/', $emailaddress)) {
        $error = "Please enter a valid email address."; //if it doesnt match the correct email format it shows this error message
    }

    if (empty($error) && empty(trim($password))) {
        $error = "Password is required"; //if the password hasnt been entered it shows this message
    }

    //if no error is recorded then find the user in the database
    if (empty($error)) {
	  
      //check can we find a user that matches the details in the database and if so, get the record  
      //prepared statements protect against SQL injection attacks
      try {
        $stmt = $pdo->prepare(" 
            SELECT user_email, user_password FROM user where user_email = ?
        ");

        //execute the query
        $stmt->execute([
            $emailaddress
        ]);
          
        $user = $stmt->fetch(PDO::FETCH_ASSOC); //this gets the user record from the database
          
          
        if (!$user) {			
          // if no user record is found
          $error = "Check your login details and try again<br>If you have forgotten your password you can reset it below";
        }	
	      
      }
      catch (PDOException $e) {
        $error = $e->getMessage(); //shows the error message
        // For debugging, this will write an error to the PHP log file 
        error_log($error);
      }      
	  
      if (empty($error) && !empty($user)) { //if the info is correctt and a record is foud in the DB
        
        if (password_verify($password, $user["user_password"])) { //compares plain text password the user entered with hashed password
          session_start(); //remembers the user for the next page
          $_SESSION["user_email"] = $user["user_email"]; //saves email in the session
          
          header("Location: dashboard.php"); //prevents the user from accesssing the dashboard without logging in
          exit();
        }
        else {
          $error = "Check your login details and try again<br>If you have forgotten your password you can reset it below";
        }
      }  	  
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
                <input type="text" name="emailaddress" id="emailaddress" class="form-control" placeholder="Email Address" value="Raheemah@hotmail.com" required>
              </div>
              <div class="mb-3">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" value="password123" required>
              </div>
              <div class="d-grid">
                <button class="btn btn-primary">Login</button>
              </div>
                       
              <div class="d-grid mt-4 mb-4 text-center">
                <a href="/account/forgottenpwd.php">Forgotten Password</a>
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