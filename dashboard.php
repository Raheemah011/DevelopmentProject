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
    $currentMonth = date('M'); //get the current month - gives a number according to 12 months in the year
    
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
      $currentHabitMonth = $dt->format('M');

      
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

rmalPriority {
  color: yellow;
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
          
          <div class="col-12 col-sm-6 col-md-3 mb-3">
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

          <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card text-center">
              <div class="card-header">
                <span class="normalPriority">Medium</span> <br>Tasks Today
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

          <div class="col-12 col-sm-6 col-md-3 mb-3">
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
          
          <div class="col-12 col-sm-6 col-md-3 mb-3">
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

          <div class="col-12 col-sm-6 col-md-3 mb-3">
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

          <div class="col-12 col-sm-6 col-md-3 mb-3">
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
                Todays Tasks
              </div>
              <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
              </div>
              <div class="card-footer text-body-secondary">
                2 days ago
              </div>
            </div>                    
          </div>
        

          <div class="col col-sm-6">
            <div class="card text-center">
              <div class="card-header">
                Todays Habits
              </div>
              <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
              </div>
              <div class="card-footer text-body-secondary">
                2 days ago
              </div>
            </div>                    
          </div> <!-- todays Habits -->


        </div> <!--second cards for habit and tasks -->
               
        
      </div>    
      
    
    </div>
      
  </div>

</body>
</html>