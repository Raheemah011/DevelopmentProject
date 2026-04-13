<?php
$pageTitle = "Password Reset"; //informs users to check their email to reset their password if forgotten
include "../header.php";
?>

<body>
  <div class="container">
    <div class="row justify-content-center mt-5">
      
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-body">
            <h3 class="text-center mb-4">Password Reset</h3>
            <img src="/files/logo.png" style="height:300px" alt="Logo" class="img-fluid mx-auto d-block mb-4">            
                        
          </div>
        </div>
      </div>
      
      
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-body">
            
            <div class="text-center mb-4">
              If your details match our system, a new password will be emailed to you. Please check your inbox and junk mail folders.
            </div>

            <div class="d-grid">
              <a class="btn btn-secondary" href="../index.php">Back</a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</body>

<?php include "../footer.php"; ?>
