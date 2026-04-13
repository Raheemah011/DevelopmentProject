<?php
$pageTitle = "Create New Account";
include "../header.php";

$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {//user submits the form
    
    //read the form data into variables
    $name = $_POST['name'];
    $emailaddress = $_POST['emailaddress'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $dateofbirth = $_POST['dateofbirth'];

    //validate the data, make sure user enters all required information
    if (empty(trim($name))) {
        $error = "Name is required";
    }
    
    if (empty($error) && empty(trim($emailaddress))) {
        $error = "Email is required";
    }
    
    if (empty($error) && empty(trim($dateofbirth))) {
        $error = "Date of birth is required";
    }
        
    if (empty($error) && empty(trim($password))) {
        $error = "Password is required";
    }
    
    if (empty($error) && empty(trim($confirmpassword))) {
        $error = "Confirm password is required";
    }
    
    //now check all values are valid - check the email address using regex    
    if (empty($error) && !preg_match('/^[\w\.\-]+@([\w\-]+\.)+[\w\-]{2,4}$/', $emailaddress)) {
        $error = "Please enter a valid email address.";
    }
    
    if (empty($error)) {
      //check dob is not in future and less than 100 years
      $dateofbirthcheck = new DateTime($dateofbirth);
      $today = new DateTime();

      if (empty($error) && $dateofbirthcheck > $today) {
         $error = "Date of birth cannot be in the future.";  
      }
      
      if (empty($error)) {
        //Get the difference - this gets a DateInterval object that can be used to get the years
        $difference = $today->diff($dateofbirthcheck);
        //use the difference DateInterval variable to years
        $age = $difference->y;

        if ($age >= 100) {
         $error = "Date of birth cannot be over 100.";  
        }
        if ($age < 12) {
          $error = "Age must be over 12.";  
        }  
      }
    }
    
    if (empty($error) && $password !== $confirmpassword) {
      $error = "Password and confirm passwords must match.";      
    }

    //check password matches the rules
    if (empty($error) && !preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password)) {
      $error = "Password must be atleast 8 charachters containing a capital letter, a number and a symbol.";      
    }
    
    //if no error is recorded then create the user
    if (empty($error)) {
      
      //only save a hash of the password, not the actual password as no one should see this ever
      //when the user logs in, their password is also hashed and can then compared to the database field value
      $passwordHash = password_hash($password, PASSWORD_DEFAULT);

      try {
        $stmt = $pdo->prepare("
            INSERT INTO user (user_name, user_email, user_password, user_dob)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $name,
            $emailaddress,
            $passwordHash,
            $dateofbirth
        ]);

        //After adding a user in the table, auto-increment primary key can be retrieved by using lastInsertId()
        $userId = $pdo->lastInsertId(); 
        
        //now automatically log the user in by starting a session and store the users id and name so it can be retrieved
        session_start();
        $_SESSION["user"] = $name;
        $_SESSION["user_id"] = $userId;
        
        //take the user to the login home page
        header("Location: ../dashboard.php");
        
        //exit - no more processing is needed
        exit();

      }
      catch (PDOException $e) {

        // specfically check for duplicate email clashes 
        if ($e->getCode() == 23000) {
            $error = "This email address is already registered.";
        } else {
            // For debugging, this will write an error to the PHP log file 
            error_log($e->getMessage());
            $error = "An error occured. Please check your details and try again.";
        }
      }      
    }
}
?>

<script>
  function DOBIsOK(value) { //check if the date of birth is valid

    if (!value) return false; //nothing entered - inbalid

    const dob = new Date(value);
    const today = new Date(); //put into a date object and get todays date

    if (dob > today) {  //in the future not allowed
      return false;
    }
    
    let age = today.getFullYear() - dob.getFullYear(); //calculate age difference
    
    if (age  > 100) { //if age is greater than 100 its invalid
      return false;      
    } 
    if (age < 12) { //if age is less than 12 its invalid
      return false;
    }
    
    return true; // otherwise its true
  }

  function validateForm() {
      return true;
      
      const emailaddress = $("#emailaddress").val().trim(); //gets the email and remove spaces
      const emailPattern = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/; //regex email format to check its valid
      
      if (!emailPattern.test(emailaddress)) {//if it doesnt match the regex pattern, it stops form sunmission
          alert("Please enter a valid emailaddress address.");
          return false;
      }
      
      if ($("#password").val() !== $("#confirmpassword").val()) {
          alert("Passwords do not match.");// if password entered doesnt match the password stored send an error
          return false;            
      }
      
      const password = $("#password").val().trim(); //remove spaces for the password
      const passwordPattern = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/; //makes sure password follows rules

      if (!passwordPattern.test(password)) {
          alert("Password must be atleast 8 charachters containing a capital letter, a number and a symbol.");
          return false;
      }
      
      if (!DOBIsOK($("#dateofbirth").val())) {
        alert("Please enter a valid date of birth");
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
            <h3 class="text-center mb-4">Create Account</h3>
            <img src="/files/logo.png" style="height:300px" alt="Logo" class="img-fluid mx-auto d-block mb-4">            
            
            <div class="text-center">
              Fill in your details to create a free account to get access to Striver.
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
                <label for="Name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" required value="Raheemah">
              </div>


              <div class="mb-3">
                <label for="dateofbirth" class="form-label">Date of Birth</label>
                <input type="date" name="dateofbirth" id="dateofbirth" class="form-control" required value="1999-01-01">
                <p class="small"> Your date of birth is used if you need to reset your password</p>
              </div>

              <div class="mb-3">
                <label for="emailaddress" class="form-label">Email Address</label>
                <input type="email" name="emailaddress" id="emailaddress" class="form-control" required value="Raheemah@hotmail.com">
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required value="Raheemah1?">
              </div>

              <div class="mb-3">
                <label for="confirmpassword" class="form-label">Confirm Password</label>
                <input type="password" name="confirmpassword" id="confirmpassword" class="form-control" required value="Raheemah1?">
                <p class="small"> 
                 Password rules: <br>
                 8 charchter <br>
                 8 charchter <br>
                 8 charchter <br>
                 </p>
              </div>
              
              <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary" onclick="return validateForm()">Create Account</button>
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
