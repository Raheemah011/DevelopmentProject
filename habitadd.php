<?php
  session_start();

  $pageTitle = "Add Habit";
  include "header.php"; //loads header file

  include "getuser.php"; //this will make $user available in this program


$error = ""; //stores error messages
$habit_name = "";
$habit_description = "";
$habit_frequency = "";
$habit_id_passedin = "";

if ($_SERVER["REQUEST_METHOD"] === "GET") { //runs if the user loads  the page for the first time
  
  if (isset($_GET['habit_id'])) {
    $habit_id_passedin = $_GET['habit_id'];
  }
     
  if (!empty($habit_id_passedin)) {
    try {
        //I use the task id and userid of the logged in persion to get the task, otherwise anyone could pass in any task id and edit that!!!
        $stmt = $pdo->prepare("
            SELECT * from habit where habit_id = ? and user_id = ?
        ");
        
        $stmt->execute([
            $habit_id_passedin ,
            $user["user_id"]
        ]);
        
      //use fetch to get this one record from the database
      $habit = $stmt->fetch(PDO::FETCH_ASSOC);

     }
      catch (PDOException $e) {

        // For debugging, this will write an error to the PHP log file 
        error_log($e->getMessage());
        $error = "An error occured. Please check task id and try again.";
      }      
      
      if (!empty($habit)) {
        $habit_name = $habit["habit_name"]; 
        $habit_description = $habit["habit_description"];
        $habit_frequency = $habit["habit_frequency"];    
      }
    
  }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") { //runs if the user submitted the form 

    //retrive the habit id if it was passed in. When the page is first loaded, if passed in, it is saved to the form as a hidden field so it can be read when the form is posted
    $habit_id_passedin = $_POST["habit_id_passedin"]; //empty means a new habit is being addded
    
    $delete = $_POST["DeleteButton"];
    
    if (!empty($delete)) {
  
        //future implementation would be to delete the logs and habits as a single transaction
        try {
          //task_id is created automatically
          $stmt = $pdo->prepare("
              DELETE from habitlog where habit_id = ?  and user_id = ?
          ");
          
          $stmt->execute([
              $habit_id_passedin,
              $user["user_id"]
          ]);

        }
        catch (PDOException $e) {

          // For debugging, this will write an error to the PHP log file 
          error_log($e->getMessage());
          $error = "An error occured. Please check your details and try again.";
        }      
   
        //if there wasnt any error deleting the logs then also delete the actual habit
        if (empty($error)) {
          try {
            //task_id is created automatically
            $stmt = $pdo->prepare("
                DELETE from habit where habit_id = ?  and user_id = ?
            ");
            
            $stmt->execute([
                $habit_id_passedin,
                $user["user_id"]
            ]);

          }
          catch (PDOException $e) {

            // For debugging, this will write an error to the PHP log file 
            error_log($e->getMessage());
            $error = "An error occured. Please check your details and try again.";
          }      
        }

      //take the user to the login home page
      header("Location: habits.php");
      
      //exit - no more processing is needed
      exit();
  
    }
    
    //retrieves the values the user entered in the form
    $habit_name = $_POST["habit_name"]; 
    $habit_description = $_POST["habit_description"];
    $habit_frequency = $_POST["habit_frequency"];

    //if the user hasnt entered info in the email field an error message is returned
    if (empty(trim($habit_name))) { //trim - removes blank spaces
        $error = $error . "<br>habit name is required"; 
    }

    if (empty(trim($habit_description))) { //trim - removes blank spaces
        $error = $error . "<br>habit description is required"; 
    }
            
    if (empty(trim($habit_frequency))) { //trim - removes blank spaces
        $error = $error . "<br>habit frequency is required"; 
    }
        
    
    //if no error is recorded then create the habit
    if (empty($error)) {

      if (empty($habit_id_passedin)) {                
        try {
          //habit_id is created automatically
          $stmt = $pdo->prepare("
              INSERT INTO habit (user_id, habit_name, habit_description, habit_frequency)
              VALUES (?, ?, ?, ?)
          ");
          
          $stmt->execute([
              $user["user_id"],
              $habit_name,
              $habit_description,
              $habit_frequency
          ]);

          //take the user to the login home page
          header("Location: habits.php");
          
          //exit - no more processing is needed
          exit();

        }
        catch (PDOException $e) {

          // For debugging, this will write an error to the PHP log file 
          error_log($e->getMessage());
          $error = "An error occured. Please check your details and try again.";
        }      
      
      } //empty($habit_id_passedin)

      //if habit_id_passedin is not empty then we are editing an existing habit
      if (!empty($habit_id_passedin)) {                
        try {
          //habit_id is created automatically
          $stmt = $pdo->prepare("
              UPDATE habit SET  habit_name = ?, habit_description = ?, habit_frequency = ?
              WHERE  habit_id = ?
              AND    user_id = ?
          ");
          
          $stmt->execute([
              $habit_name,
              $habit_description,
              $habit_frequency,
              $habit_id_passedin,
              $user["user_id"]
          ]);

          //take the user to the login home page
          header("Location: habits.php");
          
          //exit - no more processing is needed
          exit();

        }
        catch (PDOException $e) {

          // For debugging, this will write an error to the PHP log file 
          error_log($e->getMessage());
          $error = "An error occured. Please check your details and try again.";
        }      
      
      } //empty($habit_id_passedin)

      
    } //(empty($error))  so save the data    
    
} //post
      
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
               
        <div class="row mt-3 ms-2"> <!-- Main Content -->
        
          <?php if ($error): ?>
          <div class="alert alert-danger">
            <?php echo $error; ?>
          </div>
          <?php endif; ?>
        
          <form method="POST">

              <!-- if a habit id is passed in, then save it in a hidden field so this can be used to find the habit again -->
              <input type="hidden" id="habit_id_passedin" name="habit_id_passedin" value="<?= $habit_id_passedin ?>" >              
              
              <div class="mb-3">
                  <label for="habit_name" class="form-label">Habit Name (required)</label>
                  <input type="text" value="<?= htmlspecialchars($habit_name) ?>"  class="form-control" id="habit_name" name="habit_name" maxlength="255" required placeholder="Enter habit title">
              </div>

              <div class="mb-3">
                  <label for="habit_description" class="form-label">Description (required)</label>
                  <textarea class="form-control" id="habit_description" name="habit_description" rows="3" maxlength="255" placeholder="Describe the habit" required><?= htmlspecialchars($habit_description) ?></textarea>
              </div>

              <div class="row">
                  <div class="col-md-6 mb-3">
                      <label for="habit_frequency" class="form-label">Frequency (required)</label>
                      <select class="form-select" id="habit_frequency" name="habit_frequency" required>
                          <option value=""></option>
                          <option value="Daily" <?= ($habit_frequency == "Daily") ? 'selected' : '' ?> >Daily</option>
                          <option value="Weekly" <?= ($habit_frequency == "Weekly") ? 'selected' : '' ?> >Weekly</option>
                          <option value="Monthly" <?= ($habit_frequency == "Monthly") ? 'selected' : '' ?>>Monthly</option>
                      </select>
                  </div>

              </div>

              <div class="mt-3">
                  <button type="submit" class="btn btn-primary">Save habit</button>
                  
                  <?php if (empty($habit_id_passedin)) { ?>
                      <button type="reset" class="btn btn-outline-secondary">Clear</button>
                  <?php } ?>      
                  
                  <?php if (!empty($habit_id_passedin)) { ?>
                      <button  name="DeleteButton" value="Delete" onclick="return checkDeleteOK()" class="btn btn-outline-danger">Delete</button>
                  <?php } ?>      
                  

              </div>

          </form>          

          
          
        </div> <!-- Main Content -->
    
      </div> <!-- Col-10 -->
          
    </div> <!-- Main row -->
      
  </div> <!-- container-fluid -->


</body>


<script>
  
  function checkDeleteOK() {    
    var answer = confirm("Are you sure you want to delete this habit and all associated habit activity?"); //confirm is a yes-no alert box and this will give a true or false
    return answer; //returning false will automatically cancel the button click so wont delete the task
  }
  
</script>


</html>