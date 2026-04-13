<?php
$pageTitle = "Forgotten Password";
include "../header.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") { //submits users form using the post method
    
    //read the form data into variables
    $emailaddress = $_POST['emailaddress'];
    $dateofbirth = $_POST['dateofbirth'];
  
    if (empty($error) && empty(trim($emailaddress))) { //removes spaces
        $error = "Email is required"; //if email field is empty display this
    }
    
    if (empty($error) && empty(trim($dateofbirth))) {
        $error = "Date of birth is required";
    }
           
    //now check all values are valid - check the email address using regex    
    if (empty($error) && !preg_match('/^[\w\.\-]+@([\w\-]+\.)+[\w\-]{2,4}$/', $emailaddress)) {
        $error = "Please enter a valid email address.";
    }
   
    //if no error is recorded then create the user
    if (empty($error)) {

	  //check can we find a user that matches the details and if so, get the record  
      try {
        $stmt = $pdo->prepare("
            SELECT user_id, user_email FROM user where user_email = ? and user_dob = ?
        ");
 
        $stmt->execute([
            $emailaddress,
            $dateofbirth
        ]);
        
		$user = $stmt->fetch(PDO::FETCH_ASSOC);//fetch from the database 
		
		if (!$user) {			
			// no record found
			$error = "No user found with those details.";
		}		
		
      }
      catch (PDOException $e) {
		$error = $e->getMessage();
		
		// For debugging, this will write an error to the PHP log file 
		error_log($error);

      }      
    }
	
	
	if (empty($error) && $user) {
	  
        //only save a hash of the password, not the actual password as no one should see this ever
        //when the user logs in, their password is also hashed and can then compared to the database field value
        
		$password = "password123";
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            Update user SET user_password = ? where user_id =  ?
        ");

        $stmt->execute([
            $passwordHash,
            $user['user_id'],
        ]);

		mail(
			$user['user_email'],
			"Striver Password Reset",
			"Your Striver password has been reset to " . $password,
			""
		);		
		
		//take the user to the login home page
	    header("Location: /account/passwordresetdone.php");
			
		//exit - no more processing is needed
		exit();
			
			
		
	}
	
}
?>

<script>


  function validateForm() {
      return true;
      
      const emailaddress = $("#emailaddress").val().trim(); //remove spaces from email give
      const emailPattern = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/; //regex email pattern
      
      if (!emailPattern.test(emailaddress)) { //if it doesnt match the regex pattern
          alert("Please enter a valid emailaddress address.");//display this
          return false;
      }
      
      return true;
  }

</script>


<body>
  <div class="container">
    <div class="row justify-content-center mt-5">
      
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-body">
            <h3 class="text-center mb-4">Forgotten Password</h3>
            <img src="/files/logo.png" style="height:300px" alt="Logo" class="img-fluid mx-auto d-block mb-4">            
            
            <div class="text-center">
              Fill in your details to have a new password emailed to you.
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

            <form method="POST" id="mainform">

              <div class="mb-3">
                <label for="emailaddress" class="form-label">Email Address</label>
                <input type="email" name="emailaddress" id="emailaddress" class="form-control" required value="Raheemah@hotmail.com">
              </div>

              <div class="mb-3">
                <label for="dateofbirth" class="form-label">Date of Birth</label>
                <input type="date" name="dateofbirth" id="dateofbirth" class="form-control" required value="1999-01-01">
              </div>
              
              <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary" onclick="return validateForm()">Reset Password</button>
              </div>

              <div class="d-grid">
                <a class="btn btn-secondary" href="../index.php">Back</a>
              </div>
              
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</body>

<?php include "../footer.php"; ?>
