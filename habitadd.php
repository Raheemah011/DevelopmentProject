<?php
  session_start();

  $pageTitle = "Add Habit";
  include "header.php"; //loads header file

  include "getuser.php"; //this will make $user available in this program


$error = ""; //stores error messages
$habit_name = "";
$habit_description = "";
$habit_frequency = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") { //runs if the user submitted the form 
    
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
    } //(empty($error))  so save the data    
    
    
}
      
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

              <div class="d-grid gap-2 mt-3">
                  <button type="submit" class="btn btn-primary">Save habit</button>
                  <button type="reset" class="btn btn-outline-secondary">Clear</button>
              </div>

          </form>          

          
          
        </div> <!-- Main Content -->
    
      </div> <!-- Col-10 -->
          
    </div> <!-- Main row -->
      
  </div> <!-- container-fluid -->


</body>
</html>