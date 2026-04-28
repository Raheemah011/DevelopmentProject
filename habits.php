<?php
  session_start();

  $pageTitle = "Habits";
  include "header.php"; //loads header file

  include "getuser.php"; //makes $user available in this program 

$todaysDate      = date("Y-m-d");
$todaysYearMonth = date("Ym"); //Year and month 202604 - lower case m is the month as a number uppercase is like Apr for April
$todaysYearWeek  = date("oW"); // "oW" is just a way to label a week.
                               // "W" is the week number, like saying "this is week 17".
                               // "o" is the year that week really belongs to.
                               // Sometimes the first few days of January are still part of the
                               // last week from the previous year, which is a bit confusing.
                               // So "o" makes sure we use the correct year for that week.
                               // So "202617", it means week 17 in the year 2026
          
    
if ($_SERVER["REQUEST_METHOD"] === "POST") { //runs if the user submitted the form 
    
    //retrieves the hidden value that tells us which habit was toggled
    $habit_id = $_POST["habit_id"]; 

    //Check we have a habit id
    if (empty(trim($habit_id))) { //trim - removes blank spaces
        $error = $error . "<br>habit is required"; 
    }
    
    //prepare SQL query to get the selected habit
    $stmt = $pdo->prepare(" 
        SELECT * FROM habitlog where habit_id = ? and user_id = ? and habitlog_date = ?
    ");

    //execute the query
    $stmt->execute([
        $habit_id,
        $user["user_id"],
        $todaysDate        
    ]);

    //use fetch to get this one record from the database
    $habitlog = $stmt->fetch(PDO::FETCH_ASSOC);

    //doesnt exist so create it
    if (empty($habitlog)) {

        $stmt = $pdo->prepare("
            INSERT INTO habitlog (habit_id, user_id, habitlog_date, habitlog_yearmonth, habitlog_yearweek)
            VALUES (?, ?, ?, ?, ?)
        ");

        //execute the query
        $stmt->execute([
            $habit_id,
            $user["user_id"],
            $todaysDate,
            $todaysYearMonth,
            $todaysYearWeek
        ]);
        
        // now set the streak counters on the habit itself
        // to do this we need to
        // 1) Check if teh user has logged a habit in the previous period according to the streak
        // 2) If they have increase both the current streak and best streak if that is suitable.
        // 3) if they havent then restart the current streak but leave the best streak as it is.
        
        
    }
    
    //it does exist so delete it
    if (!empty($habitlog)) { 
        $stmt = $pdo->prepare("
            delete from habitlog where habit_id = ? and user_id = ? and habitlog_date = ?
        ");

        //execute the query
        $stmt->execute([
            $habit_id,
            $user["user_id"],
            $todaysDate
        ]);
      
    }

  
}

?>

<script>

  function submitform(habit_id, habit_name) {    
    if (confirm("Are you sure you want to change the habit status of " + habit_name )) {
      $('#habit_id').val(habit_id);
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
        
      <div class="col-9">  <!-- Col-10 -->
     
        <div class="row mt-3 ms-2">
          <?php include "welcomeheader.php"; ?>
        </div>
               
        <div class="row mt-3 ms-2">

          <form name=mainform" id="mainform" method="post">
            
            <input type="hidden" name="habit_id" id="habit_id">
            
            
            <a href="habitadd.php" class="col-2 btn btn-primary mb-3">+ Add Habit</a>     
            
            <table class="table table-bordered table-striped">
              <thead>
                  <tr>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Notes</th>
                      <th>Done</th>
                      <th>Date Done</th>
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
                
              <?php 
                
                
                foreach ($habits as $habit): 
              
                  //the date must be saved in the correct format - e.g 2026-04-11 so convert todays date to that format
                  $checked = "";
                  
                  if ($habit["habit_frequency"] == "Daily") {
                    $stmt = $pdo->prepare(" 
                        SELECT * FROM habitlog where user_id = ? and habit_id = ? and habitlog_date = ?
                    ");
                    //execute the query
                    $stmt->execute([
                        $habit["user_id"],
                        $habit["habit_id"],
                        $todaysDate                    
                    ]);
                  }
                  else if ($habit["habit_frequency"] == "Weekly") {
                    $stmt = $pdo->prepare(" 
                        SELECT * FROM habitlog where user_id = ? and habit_id = ? and habitlog_yearweek = ?
                    ");
                    //execute the query
                    $stmt->execute([
                        $habit["user_id"],
                        $habit["habit_id"],
                        $todaysYearWeek 
                    ]);
                  }
                  else if ($habit["habit_frequency"] == "Monthly") {
                    $stmt = $pdo->prepare(" 
                        SELECT * FROM habitlog where user_id = ? and habit_id = ? and habitlog_yearmonth = ?
                    ");
                    //execute the query
                    $stmt->execute([
                        $habit["user_id"],
                        $habit["habit_id"],
                        $todaysYearMonth
                    ]);
                  }


                  $habitlog_date = "";
                  $habitlog_dateFormatted = "";
                  
                  $habitlog = $stmt->fetch(PDO::FETCH_ASSOC);
                  if (!empty($habitlog)) {
                      $checked = "checked";
                      
                      $habitlog_date = new DateTime($habitlog['habitlog_date']);
                      $habitlog_dateFormatted = $habitlog_date->format("d/m/Y");
                      
                  }

                ?>
                  
                  
                  <tr>
                      <td><?= htmlspecialchars($habit['habit_name'] ?? '') ?></td>
                      <td><?= htmlspecialchars($habit['habit_description']  ?? '') ?></td>
                      <td><?= htmlspecialchars($habit['habit_frequency']  ?? '') ?></td>
                      
                      <!-- The value in the checkbox is the habit id so we know which habit has been updated. -->
                      <!-- When the habit is checked, the hidden field is updated so we can see which habit was updated and then the form is submitted. -->
                      <td><input type="checkbox" class="form-check-input checked-green" <?= $checked ?> onchange="submitform('<?= $habit['habit_id'] ?>', '<?= htmlspecialchars($habit['habit_name'] ?? '') ?>'); "> </td>
                      <td><?= $habitlog_dateFormatted ?></td>
                  </tr>
              <?php endforeach; ?>
               
              </tbody>
            </table>
          
          </form>
          
        </div>
    
      </div> <!-- Col-10 -->
          
    </div> <!-- Main row -->
      
  </div> <!-- container-fluid -->


</body>
</html>