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
$task_id_passedin = null;

if ($_SERVER["REQUEST_METHOD"] === "GET") { //runs if the user enters the page for first time. Check if a task was passed in and show its details. otherwise its a new task being added

  if (isset($_GET['task_id'])) {
    $task_id_passedin = $_GET['task_id'];
  }
  
  if (!empty($task_id_passedin)) {
    try {
        //I use the task id and userid of the logged in persion to get the task, otherwise anyone could pass in any task id and edit that!!!
        $stmt = $pdo->prepare("
            SELECT * from task where task_id = ? and user_id = ?
        ");
        
        $stmt->execute([
            $task_id_passedin ,
            $user["user_id"]
        ]);
        
      //use fetch to get this one record from the database
      $task = $stmt->fetch(PDO::FETCH_ASSOC);

     }
      catch (PDOException $e) {

        // For debugging, this will write an error to the PHP log file 
        error_log($e->getMessage());
        $error = "An error occured. Please check task id and try again.";
      }      
      
      if (!empty($task)) {
        $task_name = $task["task_name"]; 
        $task_description = $task["task_description"];
        $task_duedate = $task["task_duedate"]; 
        $task_priority = $task["task_priority"];        
      }
    
  }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") { //runs if the user submitted the form 

    //retrive the task id if it was passed in. When the page is first loaded, if passed in, it is saved to the form as a hidden field so it can be read when the form is posted
    $task_id_passedin = $_POST["task_id_passedin"]; //empty means a new task is being edited 
    
    $delete = $_POST["DeleteButton"];
    
    if (!empty($delete)) {
           
        try {
          //task_id is created automatically
          $stmt = $pdo->prepare("
              DELETE from task where task_id = ?  and user_id = ?
          ");
          
          $stmt->execute([
              $task_id_passedin,
              $user["user_id"]
          ]);

        }
        catch (PDOException $e) {

          // For debugging, this will write an error to the PHP log file 
          error_log($e->getMessage());
          $error = "An error occured. Please check your details and try again.";
        }      
       
      //take the user to the login home page
      header("Location: tasks.php");
      
      //exit - no more processing is needed
      exit();
  
    }
    
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
    else if (empty($task_id_passedin)) {    
      $task_duedatecheck = new DateTime($task_duedate);
      $today = new DateTime();

      if ($task_duedatecheck->format('Y-m-d') < $today->format('Y-m-d')) {
         $error = "Task due date cannot be in the past.";  
      }
    }
    
    //if no error is recorded then create the task
    if (empty($error)) {
      
      if (empty($task_id_passedin)) {
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
      }
      else {
        
        try {
          //task_id is created automatically
          $stmt = $pdo->prepare("
              UPDATE task set  task_name = ?,  task_description = ?, task_priority = ?, task_duedate = ?
              where  task_id = ? 
              and    user_id = ?
          ");
          
          $stmt->execute([
              $task_name,
              $task_description,
              $task_priority,
              $task_duedate,
              $task_id_passedin,
              $user["user_id"]
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
              <!-- if a task id is passed in, then save it in a hidden field so this can be used to find the task again -->
              <input type="hidden" id="task_id_passedin" name="task_id_passedin" value="<?= $task_id_passedin ?>" >
              
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
                          <option value="2" <?= ($task_priority == "2") ? 'selected' : '' ?> >Medium</option>
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
                  
                  <?php if (empty($task_id_passedin)) { ?>
                      <button type="reset" class="btn btn-outline-secondary">Clear</button>
                  <?php } ?>      
                  
                  <?php if (!empty($task_id_passedin)) { ?>
                      <button  name="DeleteButton" value="Delete" onclick="return checkDeleteOK()" class="btn btn-outline-danger">Delete</button>
                  <?php } ?>      
              </div>

          </form>          

          
          
        </div> <!-- Main Content -->
    
      </div> <!-- col-9 -->
          
    </div> <!-- Main row -->
      
  </div> <!-- container-fluid -->


</body>

<script>
  
  function checkDeleteOK() {    
    var answer = confirm("Are you sure you want to delete this task?"); //confirm is a yes-no alert box and this will give a true or false
    return answer; //returning false will automatically cancel the button click so wont delete the task
  }
</script>

</html>