<?php

  session_start(); //start session to access session variables

  $pageTitle = "Dashboard";
  include "header.php"; //loads header file

  include "getuser.php"; //check is theuser is logged in with session variables and see if it exists
      
?>

<body>

  <div class="container-fluid">
        
    <div class="row">
      <div class="col-2">
        <?php include "menu.php"; ?> <!--this creates the side bar including logo, menu, logout-->
      </div>
        
      <div class="col-9"> <!--main page is 10 columns big-->
     
        <div class="row mt-3 ms-2">
          <?php include "welcomeheader.php"; ?> <!--welcome message with users email-->
        </div>
               
        <div class="row mt-3 ms-2"> <!--put cards on to the page using bootstrap - replace placeholders -->
          
          <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card text-center">
              <div class="card-header">
                Tasks Today
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

          <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card text-center">
              <div class="card-header">
                Habits Today
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
        

          <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card text-center">
              <div class="card-header">
                Best Streak
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
        

          <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card text-center">
              <div class="card-header">
                Badges Earned
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
        
        
        <div class="row mt-3 ms-2"> <!--the second cards for habit and tasks -->
        
          <div class="col col-sm-6">
            <div class="card text-center">
              <div class="card-header">
                Todays Tasks
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
                Todays Habits
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
          </div> <!-- todays Habits -->


        </div> <!--second cards for habit and tasks -->
               
        
      </div>    
      
    
    </div>
      
  </div>

</body>
</html>