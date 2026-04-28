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


//Need to get the previous periods so I can calculate the streaks properly          
$previousDate = date("Y-m-d", strtotime("-1 day")); // previous day
$previousYearMonth = date("Ym", strtotime("first day of last month")); // Previous month 
$previousYearWeek = date("oW", strtotime("-1 week")); // Previous week 
$addedOrDeleted = ""; //use this to track if i have added or removed a checkmark against the habit
    
if ($_SERVER["REQUEST_METHOD"] === "POST") { //runs if the user submitted the form 
    
    //retrieves the hidden value that tells us which habit was toggled
    $habit_id = $_POST["habit_id"]; 

    //Check we have a habit id
    if (empty(trim($habit_id))) { //trim - removes blank spaces
        $error = $error . "<br>habit is required"; 
    }


    if (empty($error)) {

      //Read the habit record for the selected habit id
      $stmt = $pdo->prepare(" 
          SELECT * FROM habit where habit_id = ? and user_id = ?
      ");

      //execute the query
      $stmt->execute([
          $habit_id,
          $user["user_id"]   
      ]);

      //use fetch to get this one record from the database
      $habit = $stmt->fetch(PDO::FETCH_ASSOC);


      //need to read the correct habitlog record based on the frequency of the habit, not just todays date as that was a bug
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

      //use fetch to get this one record from the database
      $habitlog = $stmt->fetch(PDO::FETCH_ASSOC);

      //doesnt exist so create it
      if (empty($habitlog)) {
          //set this so I can tell later if streak needs to be added to

          $addedOrDeleted = "ADDED";
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
      }
      
      //if habitlog record already exists, then taht means we want to delete it instead as user will have ticked to uncheck it
      //Cant just delete using the date, have to check the habit frequency and then delete using the correct variables
      if (!empty($habitlog)) { 
          //set this so I can tell later if streak needs to be reduced
          $addedOrDeleted = "DELETED";
            
          if ($habit["habit_frequency"] == "Daily") {
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
          else if ($habit["habit_frequency"] == "Weekly") {
            $stmt = $pdo->prepare("
                delete from habitlog where habit_id = ? and user_id = ? and habitlog_yearweek = ?
            ");

            //execute the query
            $stmt->execute([
                $habit_id,
                $user["user_id"],
                $todaysYearWeek
            ]);
          }
          else  {
            $stmt = $pdo->prepare("
                delete from habitlog where habit_id = ? and user_id = ? and habitlog_yearmonth = ?
            ");

            //execute the query
            $stmt->execute([
                $habit_id,
                $user["user_id"],
                $todaysYearMonth
            ]);
          }
        
      }
      
      // now set the streak counters on the habit itself
      // to do this:
      // 1) Check if the user has logged a habit in the previous period according to the frequency
      // 2) If they have continued the habit (previous exists) then increase both the current streak and best streak if that is suitable.
      // 3) if they have missed a habit then restart the current streak but leave the best streak as it is.
      // 4) if the have unchecked an existing habit, then reduce the current streak and best streak if suitable


      //now readd the previous habitlog entry if it exists to check streaks
      if ($habit["habit_frequency"] == "Daily") {
        $stmt = $pdo->prepare(" 
            SELECT * FROM habitlog where user_id = ? and habit_id = ? and habitlog_date = ?
        ");
        //execute the query
        $stmt->execute([
            $habit["user_id"],
            $habit["habit_id"],
            $previousDate                    
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
            $previousYearWeek 
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
            $previousYearMonth
        ]);
      }


      //use fetch to get this one previous habitlog record from the database
      $habitlogPrevious = $stmt->fetch(PDO::FETCH_ASSOC);

      //get the current streak counts into variables so they can be amended and then use them to update the habi streaks
      $currentStreak = $habit["habit_currentstreak"];
      $bestStreak = $habit["habit_beststreak"];

      if (empty($currentStreak)) { //if its null set to 0
        $currentStreak = 0;
      }

      if (empty($bestStreak)) { //if its null set to 0
        $bestStreak = 0;
      }
      
      //if we added a habit we want to increase the streak counts
      if ($addedOrDeleted == "ADDED") {       
        if (empty($habitlogPrevious)) { //user did not record habit for last period so restart the streak
            $currentStreak = 1;
            //if never set then set it for first time, bug was setting beststreak to 0 if previos didnt exits everytime!
            if ($bestStreak == 0) {
              $bestStreak = 1;
            }
        }
        
        else { //previous does exist so we can increment it
          $currentStreak = $currentStreak + 1;
          if ($currentStreak  > $bestStreak) {
            $bestStreak = $currentStreak;           
          }
        }
      }

      //if we undid a habit we want to decrease the streak counts but make sure they dont go less than 0
      if ($addedOrDeleted == "DELETED") {       
         if ($bestStreak == $currentStreak) { //if the current streak is also the  best, then we also need to reduce the best streak
           $bestStreak = $bestStreak - 1;
         }
         $currentStreak = $currentStreak - 1;
         
         //bug - check against negative values!
         if ($currentStreak < 0) {
           $currentStreak = 0;
         }

         if ($bestStreak < 0) {
           $bestStreak = 0;
         }
    
      }
      
      //now save the new streak values
      $stmt = $pdo->prepare("
          UPDATE habit 
          SET habit_currentstreak = ?, habit_beststreak = ?
          WHERE habit_id = ? AND user_id = ?
      ");
      
      //execute the query
      $stmt->execute([
          $currentStreak,
          $bestStreak,
          $habit["habit_id"],
          $habit["user_id"]                
      ]);
  
      
    } //error is empty - i.e habit_id was passed in

  
}

?>

<script>

  function submitform(habit_id, habit_name) {    
    if (confirm("Are you sure you want to change the habit status of " + habit_name )) {
      //set the hidden field habit_id so this can be read and update when the form is submitted
      $('#habit_id').val(habit_id);
      //submit the form
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
                      <th>Current</th>
                      <th>Best</th>
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
                
                //get all the habits for this user
                $habits = $stmt->fetchAll(PDO::FETCH_ASSOC);
               
              ?>
                
              <?php 
                
                //cycle through each habit and see if we have a current habitlog based on the frequency
                foreach ($habits as $habit): 
              
                  //set the checked variable to blank, it will be used to set the checbox to checked if a habit has been logged
                  $checked = "";
                  
                  //now get correct habitlog according to the frequency on the habitlog
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

                  //clear the dispay date variables and set if a habit log record is found
                  $habitlog_date = "";
                  $habitlog_dateFormatted = "";
                  
                  $habitlog = $stmt->fetch(PDO::FETCH_ASSOC);
                             
                  //if a current habitlog exists accoring to the frequency then we can set "checked" and also set the date field so we can show when it was logged
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
                      <td><?= htmlspecialchars($habit['habit_currentstreak']  ?? '') ?></td>
                      <td><?= htmlspecialchars($habit['habit_beststreak']  ?? '') ?></td>
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