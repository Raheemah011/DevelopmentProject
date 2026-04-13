<?php
  session_start();

  $pageTitle = "Add Task";
  include "header.php"; //loads header file

  include "getuser.php"; 
      
?>

<body>

  <div class="container-fluid">
        
    <div class="row"> <!-- Main row -->
      <div class="col-2">
        <?php include "menu.php"; ?>
      </div>
        
      <div class="col-10">  <!-- Col-10 -->
     
        <div class="row mt-3 ms-2">
          <?php include "welcomeheader.php"; ?>
        </div>
               
        <div class="row mt-3 ms-2"> <!-- Main Content -->
          
        </div> <!-- Main Content -->
    
      </div> <!-- Col-10 -->
          
    </div> <!-- Main row -->
      
  </div> <!-- container-fluid -->


</body>
</html>