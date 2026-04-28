<?php
  session_start();

  $pageTitle = "Tasks";
  include "header.php"; //loads header file

  include "getuser.php"; 


if ($_SERVER["REQUEST_METHOD"] === "POST") { //runs if the user submitted the form 
    
    //retrieves the hidden value that tells us which task was toggled
    $task_id = $_POST["task_id"]; 

    //Check we have a task id
    if (empty(trim($task_id))) { //trim - removes blank spaces
        $error = $error . "<br>task id is required"; 
    }
    
    //prepare SQL query to get the selected task
    $stmt = $pdo->prepare(" 
        SELECT * FROM task where task_id = ?
    ");

    //execute the query
    $stmt->execute([
        $task_id
    ]);

    //use fetch to get this one record from the database
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    //set completed to null at first
    //if the date has been set, then this will clear it.
    //if the date has not been set, then i will set the date to todays date and this will indicate the task is done
    
    $completeddate = null;
    
    if (empty($task["task_completeddate"])) {
      //the date must be saved in the correct format - e.g 2026-04-11 so convert todays date to that format
      $completeddate = date("Y-m-d");
    }
    
    //now update the date on the specific task in the database
    $stmt = $pdo->prepare(" 
        update task set task_completeddate = ? where task_id = ?
    ");

    //execute the query
    $stmt->execute([
        $completeddate,
        $task_id
    ]);
    
}

      
?>

<script>

  function submitform(task_id,task_name) {    
    if (confirm("Are you sure you want to change the task status of " + task_name )) {
      $('#task_id').val(task_id);
      $("#mainform").submit();     
    }
  }
  
</script>

<style>
.checked-green:checked {
  background-color: green;
  border-color: green;
}
</style>


<body>

  <div class="container-fluid">
        
    <div class="row"> <!-- Main row -->
      <div class="col-2">
        <?php include "menu.php"; ?>
      </div>
        
      <div class="col-9">  <!-- col-9 -->
     
        <div class="row mt-3 ms-2">
          <?php include "welcomeheader.php"; ?>
        </div>
               
        <div class="row mt-3 ms-2">
          <form name=mainform" id="mainform" method="post">
            
            <input type="hidden" name="task_id" id="task_id">
            
            <a href="taskadd.php" class="btn btn-primary mb-3 col-2">+ Add Task</a>     
            
            <table class="table table-bordered table-striped">
              <thead>
                  <tr>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Priority</th>
                      <th>Due Date</th>
                      <th>Done</th>
                      <th>Completed On</th>
                  </tr>
              </thead>
              
              <tbody>
              
              <?php 
                  
                //prepare SQL query to get all tasks for the logged in user
                $stmt = $pdo->prepare(" 
                    SELECT * FROM task where user_id = ?
                ");

                //execute the query using the logged in users id
                $stmt->execute([
                    $user["user_id"]
                ]);
                
                //fetch all tasks from the database and store them in an array
                $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
              ?>
              
              <?php foreach ($tasks as $task): //this runs through every task in our returned data
                 
                  $checkeddateformatted = "";
                  $duedateformatted = ""; 
                  $priorityformatted = ""; 
                  
                  if (!empty($task['task_priority'])) {
                      if ($task['task_priority'] == "1") { 
                        $priorityformatted = "Low" ;
                      }
                      else if ($task['task_priority'] == "2") { 
                        $priorityformatted = "Medium";
                      }
                      else if ($task['task_priority'] == "3") {
                        $priorityformatted = "High";
                      }
                  } 
                  
                  if (!empty($task['task_duedate'])) {
                      //convert the database field to a date variable so we can show this in dd/mm/yyyy format
                      $duedate = new DateTime($task['task_duedate']);
                      $duedateformatted = $duedate->format("d/m/Y");
                  } 
                  
                  
                  if (!empty($task['task_completeddate'])) {
                      $checkedate = new DateTime($task['task_completeddate']);
                      $checkeddateformatted = $checkedate->format("d/m/Y");
                  } 
                  
                  //add variable that we set the "checked" if the task has a completed date and we can use that to set the checkbox status to checked
                  $checked = "";
                  if (!empty($task['task_completeddate'])) $checked = "checked";
              ?>
                  <tr>
                      <td><?= htmlspecialchars($task['task_name'] ?? '') ?></td>
                      <td><?= htmlspecialchars($task['task_description']  ?? '') ?></td>
                      <td><?= $priorityformatted ?></td>
                      <!-- The dates are shown in the correct format instead of e.g 2026-04-11 -->
                      <td><?= $duedateformatted ?></td>

                      <!-- The value in the checkbox is the task id so we know which task has been updated. -->
                      <!-- When the task is checked, the hidden field is updated so we can see which task was updated and then the form is submitted. -->
                      <td><input type="checkbox" class="form-check-input checked-green" <?= $checked ?> onchange="submitform('<?= $task['task_id'] ?>', '<?= htmlspecialchars($task['task_name'] ?? '') ?>'); "> </td>
                      <td><?= $checkeddateformatted ?></td>
                  </tr>
              <?php endforeach; ?>
               
              </tbody>
            </table>
            
          </form>
          
        </div>
    
      </div> <!-- col-9 -->
          
    </div> <!-- Main row -->
      
  </div> <!-- container-fluid -->


</body>
</html>