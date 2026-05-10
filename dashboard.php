<?php

  session_start(); //start session to access session variables

  $pageTitle = "Dashboard";
  include "header.php"; //loads header file

  include "getuser.php"; //check is theuser is logged in with session variables and see if it exists
  
  $todaysDate = date('Y-m-d');
  $taskCountTodayLow = 0;
  $taskCountTodayMedium = 0;
  $taskCountTodayHigh = 0;

  $taskCountTodayLowDone = 0;
  $taskCountTodayMediumDone = 0;
  $taskCountTodayHighDone = 0;
  $taskDone = false;



$todaysDate      = date("Y-m-d");
$todaysYearMonth = date("Ym"); //Year and month 202604 - lower case m is the month as a number uppercase is like Apr for April
$todaysYearWeek  = date("oW"); // "oW" is just a way to label a week.
                               // "W" is the week number, like saying "this is week 17".
                               // "o" is the year that week really belongs to.
                               // Sometimes the first few days of January are still part of the
                               // last week from the previous year, which is a bit confusing.
                               // So "o" makes sure we use the correct year for that week.
                               // So "202617", it means week 17 in the year 2026

    
if ($_SERVER["REQUEST_METHOD"] === "GET") { //runs when users comes into the screen
       
    //prepare SQL query to get todays tasks
    $stmt = $pdo->prepare(" 
        SELECT * FROM task where task_duedate = ?
    ");

    //execute the query
    $stmt->execute([
        $todaysDate
    ]);

    //use fetch to get this one record from the database
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($tasks as $task): //this runs through every task in our returned data
      $taskDone = !empty($task["task_completeddate"]);
      
      if ($task["task_priority"] = "1") {
        $taskCountTodayLow = $taskCountTodayLow + 1;  
        if ($taskDone) {
          $taskCountTodayLowDone = $taskCountTodayLowDone + 1;  
        }
      } 
      else if ($task["task_priority"] = "2") {
        $taskCountTodayMedium = $taskCountTodayMedium + 1;  
        if ($taskDone) {
          $taskCountTodayMediumDone = $taskCountTodayMediumDone + 1;  
        }
      }
      else {
        $taskCountTodayHigh = $taskCountTodayHigh + 1;  
        if ($taskDone) {
          $taskCountTodayHighDone = $taskCountTodayHighDone + 1;  
        }
      }
    endforeach;

    //get the first and last days of the tracking peroid so we can show the stats for this period
    //need to go one month either side of the current month as a week may start in the previous month or end in the next month
    
    $startOfMonth = (new DateTime('first day of last month'))->format('Y-m-d');
    $endOfMonth = (new DateTime('last day of next month'))->format('Y-m-d');
    
    //prepare SQL query to get todays tasks
    $stmt = $pdo->prepare(" 
      SELECT habitlog.*, habit.habit_frequency
      FROM habitlog
      JOIN habit ON habitlog.habit_id = habit.habit_id
      WHERE habitlog.habitlog_date >= ?
        AND habitlog.habitlog_date <= ?        
    ");

    //execute the query
    $stmt->execute([
        $startOfMonth,
        $endOfMonth
    ]);

    
    //use fetch to get this one record from the database
    $habitlogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $currentWeek = date('W'); //get the current week - gives a number according to 52 weeks in the year
    $currentMonth = date('m'); //get the current month - gives a number according to 12 months in the year
    
    $habitCountToday = 0;
    $habitCountWeek = 0;
    $habitCountMonth = 0;
    $habitCountTodayDone = 0;
    $habitCountWeekDone = 0;
    $habitCountMonthDone = 0;

    
    foreach ($habitlogs as $habitlog): //this runs through every task in our returned data
     
      $dt = new DateTime($habitlog['habitlog_date']);
      
      $habitlogDate = $dt->format('Y-m-d');
      $currentHabitWeek = $dt->format('W');
      $currentHabitMonth = $dt->format('m');

      
      if ($habitlog["habit_frequency"] == "Weekly" && $currentWeek == $currentHabitWeek) $habitCountWeekDone = $habitCountWeekDone + 1; 
      if ($habitlog["habit_frequency"] == "Monthly" && $currentMonth == $currentHabitMonth) $habitCountMonthDone = $habitCountMonthDone + 1; 
      if ($habitlog["habit_frequency"] == "Daily" && $todaysDate == $habitlogDate) $habitCountTodayDone = $habitCountTodayDone + 1; 
      
    endforeach;
    
    
    //prepare SQL query to get todays tasks
    $stmt = $pdo->prepare(" 
      SELECT * from habit
    ");

    //execute the query
    $stmt->execute();
    
    //use fetch to get this one record from the database
    $habits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($habits as $habit): //this runs through every task in our returned data
     
      if ($habit["habit_frequency"] == "Weekly") $habitCountWeek = $habitCountWeek + 1; 
      if ($habit["habit_frequency"] == "Monthly") $habitCountMonth= $habitCountMonth + 1; 
      if ($habit["habit_frequency"] == "Daily") $habitCountToday = $habitCountToday + 1; 
      
    endforeach;


}

?>

<style>

.lowPriority {
  color: green;
}

.mediumPriority {
  color: orange;
}

.highPriority {
  color: red;
}


</style>


<body>

  <div class="container-fluid">
        
    <div class="row">
      <div class="col-2">
        <?php include "menu.php"; ?> <!--this creates the side bar including logo, menu, logout-->
      </div>
        
      <div class="col-9"> <!--main page is 10 columns big-->
     
        <div class="row mt-3 ms-2">
          <?php include "welcomeheader.php"; ?> <!--welcome message with users email-->
        </div>
               
        <div class="row mt-3 ms-2"> <!--put cards on to the page using bootstrap - replace placeholders -->
          
          <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="card text-center">
              <div class="card-header">
                <span class="lowPriority">Low</span> <br>Tasks Today
              </div>
              <div class="card-body">
                <h5 class="card-title"></h5>
                <p class="card-text"><?= $taskCountTodayLow ?></p>
              </div>
              <div class="card-footer text-body-secondary">
                <p class="card-text">Completed <?= $taskCountTodayLowDone ?></p>
              </div>
            </div>                    
          </div>

          <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="card text-center">
              <div class="card-header">
                <span class="mediumPriority">Medium</span> <br>Tasks Today
              </div>
              <div class="card-body">
                <h5 class="card-title"></h5>
                <p class="card-text"><?= $taskCountTodayMedium ?></p>
              </div>
              <div class="card-footer text-body-secondary">
                <p class="card-text">Completed <?= $taskCountTodayMediumDone ?></p>
              </div>
            </div>                    
          </div>

          <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="card text-center">
              <div class="card-header">
                <span class="highPriority">High</span> <br>Tasks Today
              </div>
              <div class="card-body">
                <h5 class="card-title"></h5>
                <p class="card-text"><?= $taskCountTodayHigh ?></p>
              </div>
              <div class="card-footer text-body-secondary">
                <p class="card-text">Completed <?= $taskCountTodayHighDone ?></p>
              </div>
            </div>                    
          </div>
        
        </div>


        <div class="row mt-3 ms-2"> <!--put cards on to the page using bootstrap - replace placeholders -->
          
          <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="card text-center">
              <div class="card-header">
                Today's Habits
              </div>
              <div class="card-body">
                <h5 class="card-title"></h5>
                <p class="card-text"><?= $habitCountToday ?></p>
              </div>
              <div class="card-footer text-body-secondary">
                <p class="card-text">Completed <?= $habitCountTodayDone ?></p>
              </div>
            </div>                    
          </div>

          <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="card text-center">
              <div class="card-header">
                Weekly Habits
              </div>
              <div class="card-body">
                <h5 class="card-title"></h5>
                <p class="card-text"><?= $habitCountWeek ?></p>
              </div>
              <div class="card-footer text-body-secondary">
                <p class="card-text">Completed <?= $habitCountWeekDone ?></p>
              </div>
            </div>                    
          </div>

          <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="card text-center">
              <div class="card-header">
                Monthly Habits
              </div>
              <div class="card-body">
                <h5 class="card-title"></h5>
                <p class="card-text"><?= $habitCountMonth ?></p>
              </div>
              <div class="card-footer text-body-secondary">
                <p class="card-text">Completed <?= $habitCountMonthDone ?></p>
              </div>
            </div>                    
          </div>
        
        </div>

        
        <div class="row mt-3 ms-2"> <!--the second cards for habit and tasks -->
        
          <div class="col col-sm-6">
            <div class="card text-center">
              <div class="card-header">
                Incomplete Tasks
              </div>
              <div class="card-body">


                 <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Priority</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                    
                    <?php 
                        
                      //prepare SQL query to get all tasks for the logged in user
                      $stmt = $pdo->prepare(" 
                          SELECT * FROM task where user_id = ? and task_completeddate is null order by task_duedate, task_priority desc
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
                              $priorityformatted = "<span class='lowPriority'>Low</span>" ;
                            }
                            else if ($task['task_priority'] == "2") { 
                              $priorityformatted = "<span class='mediumPriority'>Medium</span>";
                            }
                            else if ($task['task_priority'] == "3") {
                              $priorityformatted = "<span class='highPriority'>High</span>";
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
                        </tr>
                    <?php endforeach; ?>
                     
                    </tbody>
                  </table>
       
              </div>
            </div>                    
          </div>
        

          <div class="col col-sm-6">
            <div class="card text-center">
              <div class="card-header">
                 Incomplete Habits 
              </div>
              <div class="card-body">

                <table class="table table-bordered table-striped">
                  <thead>
                      <tr>
                          <th>Name</th>
                          <th>Description</th>
                          <th>Notes</th>
                          <th>Current</th>
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

                      $habitlog = $stmt->fetch(PDO::FETCH_ASSOC);
                                 
                      //if a current habitlog exists accoring to the frequency then we it means the user logged this habit, so we can 
                      //skip to the next cycle in the for each loop by doing continue
                      if (!empty($habitlog)) continue;

                    ?>
                      
                      
                      <tr>
                          <td><?= htmlspecialchars($habit['habit_name'] ?? '') ?></td>
                          <td><?= htmlspecialchars($habit['habit_description']  ?? '') ?></td>
                          <td><?= htmlspecialchars($habit['habit_frequency']  ?? '') ?></td>
                          <td><?= htmlspecialchars($habit['habit_currentstreak']  ?? '') ?></td>
                      </tr>
                      
                 <?php endforeach; ?>
                   
                  </tbody>
                </table>


              </div>
            </div>                    
          </div> <!-- todays Habits -->


        </div> <!--second cards for habit and tasks -->
               
        
      </div>    
      
    
    </div>
      
  </div>

</body>
</html>