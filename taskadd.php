<?php
  session_start();

  $pageTitle = "Add Task";
  include "header.php"; //loads header file

  include "getuser.php"; //this will make $user available in this program


$error = ""; //stores error messages
$task_name = "";
$task_description = "";
$task_duedate = "";
$task_priority = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") { //runs if the user submitted the form 
    
    //retrieves the values the user entered in the form
    $task_name = $_POST["task_name"]; 
    $task_description = $_POST["task_description"];
    $task_duedate = $_POST["task_duedate"]; 
    $task_priority = $_POST["task_priority"];

    //if the user hasnt entered info in the email field an error message is returned
    if (empty(trim($task_name))) { //trim - removes blank spaces
        $error = $error . "<br>Task name is required"; 
    }
            
    if (empty(trim($task_priority))) { //trim - removes blank spaces
        $error = $error . "<br>Task priority is required"; 
    }
        
    if (empty(trim($task_duedate))) { //trim - removes blank spaces
        $error = $error . "<br>Task due date is required"; 
    }
    else {    
      $task_duedatecheck = new DateTime($task_duedate);
      $today = new DateTime();

      if ($task_duedatecheck->format('Y-m-d') < $today->format('Y-m-d')) {
         $error = "Task due date cannot be in the past.";  
      }
    }
    
    //if no error is recorded then create the task
    if (empty($error)) {
      
      try {
        //task_id is created automatically
        $stmt = $pdo->prepare("
            INSERT INTO task (user_id, task_name, task_description, task_priority, task_duedate)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $user["user_id"],
            $task_name,
            $task_description,
            $task_priority,
            $task_duedate
        ]);

        //take the user to the login home page
        header("Location: tasks.php");
        
        //exit - no more processing is needed
        exit();

      }
      catch (PDOException $e) {

        // For debugging, this will write an error to the PHP log file 
        error_log($e->getMessage());
        $error = "An error occured. Please check your details and try again.";
      }      
    } //(empty($error))  so save the data    
    
    
}
      
?>

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
               
        <div class="row mt-3 ms-2"> <!-- Main Content -->
        
          <?php if ($error): ?>
          <div class="alert alert-danger">
            <?php echo $error; ?>
          </div>
          <?php endif; ?>
        
          <form method="POST">
              
              <div class="mb-3">
                  <label for="task_name" class="form-label">Task Name (required)</label>
                  <input type="text" value="<?= htmlspecialchars($task_name) ?>"  class="form-control" id="task_name" name="task_name" maxlength="255" required placeholder="Enter task title">
              </div>

              <div class="mb-3">
                  <label for="task_description" class="form-label">Description (Optional)</label>
                  <textarea class="form-control" id="task_description" name="task_description" rows="3" maxlength="255" placeholder="Describe the task"><?= htmlspecialchars($task_description) ?></textarea>
              </div>

              <div class="row">
                  <div class="col-md-6 mb-3">
                      <label for="task_priority" class="form-label">Priority Level (required)</label>
                      <select class="form-select" id="task_priority" name="task_priority" required>
                          <option value=""></option>
                          <option value="1" <?= ($task_priority == "1") ? 'selected' : '' ?> >Low</option>
                          <option value="2" <?= ($task_priority == "2") ? 'selected' : '' ?> >Normal</option>
                          <option value="3" <?= ($task_priority == "3") ? 'selected' : '' ?>>High</option>
                      </select>
                  </div>

                  <div class="col-md-6 mb-3">
                      <label for="task_duedate" class="form-label">Due Date (required)</label>
                      <input type="date" class="form-control" value="<?= htmlspecialchars($task_duedate) ?>" id="task_duedate" name="task_duedate" required>
                  </div>
              </div>

              <div class="mt-3">
                  <button type="submit" class="btn btn-primary">Save Task</button>
                  <button type="reset" class="btn btn-outline-secondary">Clear</button>
              </div>

          </form>          

          
          
        </div> <!-- Main Content -->
    
      </div> <!-- col-9 -->
          
    </div> <!-- Main row -->
      
  </div> <!-- container-fluid -->


</body>
</html>