<?php
  session_start();

  $pageTitle = "Tasks";
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
               
        <div class="row mt-3 ms-2">
          
          <a href="addtask.php" class="btn btn-primary mb-3">+ Add Task</a>     
          
          <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Completed Date</th>
                    <th>Notes</th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 

              $stmt = $pdo->prepare(" 
                  SELECT * FROM task where user_id = ?
              ");

              //execute the query
              $stmt->execute([
                  $user["user_id"]
              ]);

              $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
              
            ?>
            
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['task_name']) ?></td>
                    <td><?= htmlspecialchars($task['task_description']) ?></td>
                    <td><?= htmlspecialchars($task['task_priority']) ?></td>
                    <td><?= htmlspecialchars($task['task_duedate']) ?></td>
                    <td><?= htmlspecialchars($task['task_completeddate']) ?></td>
                    <td><?= htmlspecialchars($task['task_completednotes']) ?></td>
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