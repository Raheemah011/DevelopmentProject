\<?php

$pageTitle = "Dashboard";
$ShowNavBar = true;
include "header.php"; //loads header file

session_start(); //start session to access session variables

if (!isset($_SESSION["user_email"])) {
    header("Location: index.php"); //if it isnt set go back to the log in page
    exit;
}
?>

<body>
<!--this creates the side bar including logo, menu, logout-->
  <div class="container-fluid">
    
    
    <div class="row">
    
      <div class="col col-2">
        <div class="row">
          <div class="col">
            <img src="/files/logo.png" style="height:150px" alt="Logo" class="img-fluid ms-2 d-block mb-4">            
          </div>
        </div>
        
        <div class="row">
          <div class="col">
            
            <div class="row mt-3 ms-2">
              <div class="col">
                Menu
              </div>
            </div>

            <div class="row mt-3 ms-2">
              <div class="col">
                Menu
              </div>
            </div>
            
            <div class="row mt-3 ms-2">
              <div class="col">
                Menu
              </div>
            </div>
            
            <div class="footer row mt-3 ms-2">
              <div class="col">
                <a href="../account/logout.php" class="btn btn-danger mt-3">Logout</a>     
              </div>
            </div>


          </div>        
        </div>
      </div>
        
     <!--main page is 10 columns big-->
     <!--put cards on to the page using bootstrap - replace placeholders -->
     <div class="col col-10">
     
        <div class="row mt-3 ms-2">
          <div class="col">
            <h2>Welcome <?php echo $_SESSION["user_email"]; ?></h2>
          </div>
        </div>
               
        <div class="row mt-3 ms-2">
          
          <div class="col col-3">
            <div class="card text-center">
              <div class="card-header">
                Featured
              </div>
              <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
              </div>
              <div class="card-footer text-body-secondary">
                2 days ago
              </div>
            </div>                    
          </div>

          <div class="col col-3">
            <div class="card text-center">
              <div class="card-header">
                Featured
              </div>
              <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
              </div>
              <div class="card-footer text-body-secondary">
                2 days ago
              </div>
            </div>                    
          </div>
        

          <div class="col col-3">
            <div class="card text-center">
              <div class="card-header">
                Featured
              </div>
              <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
              </div>
              <div class="card-footer text-body-secondary">
                2 days ago
              </div>
            </div>                    
          </div>
        

          <div class="col col-3">
            <div class="card text-center">
              <div class="card-header">
                Featured
              </div>
              <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
              </div>
              <div class="card-footer text-body-secondary">
                2 days ago
              </div>
            </div>                    
          </div>
        
        </div>
        
        
        <div class="row mt-3 ms-2">
        
          <div class="col col-sm-6">
            <div class="card text-center">
              <div class="card-header">
                Featured
              </div>
              <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
              </div>
              <div class="card-footer text-body-secondary">
                2 days ago
              </div>
            </div>                    
          </div>
        

          <div class="col col-sm-6">
            <div class="card text-center">
              <div class="card-header">
                Featured
              </div>
              <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
              </div>
              <div class="card-footer text-body-secondary">
                2 days ago
              </div>
            </div>                    
          </div>


        </div>
               
        

      </div>    
      
    
    </div>
      
    


  </div>


</body>
</html>