<?php
  session_start();

  $pageTitle = "Habits";
  include "header.php"; //loads header file

  include "getuser.php"; //makes $user available in this program 
      
?>

<body>

  <div class="container-fluid">
        
    <div class="row"> <!-- Main row -->
      <div class="col-2">
        <?php include "menu.php"; ?>
      </div>
        
      <div class="col-9">  <!-- Col-10 -->
     
        <div class="row mt-3 ms-2">
          <?php include "welcomeheader.php"; ?>
        </div>
               
        <div class="row mt-3 ms-2">
          
          <a href="habitadd.php" class="btn btn-primary mb-3">+ Add Habit</a>     
          
          <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Notes</th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 

              $stmt = $pdo->prepare(" 
                  SELECT * FROM habit where user_id = ?
              ");

              //execute the query
              $stmt->execute([
                  $user["user_id"]
              ]);

              $habits = $stmt->fetchAll(PDO::FETCH_ASSOC);
              
            ?>
              
            <?php foreach ($habits as $habit): ?>
                <tr>
                    <td><?= htmlspecialchars($habit['habit_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($habit['habit_description']  ?? '') ?></td>
                    <td><?= htmlspecialchars($habit['habit_frequency']  ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
             
            </tbody>
          </table>
          
        </div>
    
      </div> <!-- Col-10 -->
          
    </div> <!-- Main row -->
      
  </div> <!-- container-fluid -->


</body>
</html>