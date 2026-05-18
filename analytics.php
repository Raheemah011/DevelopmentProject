<?php
  session_start();

  $pageTitle = "Tasks";
  include "header.php"; //loads header file

  include "getuser.php"; 
      
?>

<body onload="loadCharts()">

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
          
            <div class="row g-4"> <!-- g-4 adds a gap between the cards -->

              <div class="col-md-6">
                  <div class="card "> 
                      <div class="card-body">
                          <h5 class="card-title">Habit Consistency</h5>
                          <canvas id="chartConsistency"></canvas>
                          <p class="card-text text-muted">This shows how many habits you logged over the past 7 days.</p>
                      </div>
                  </div>
              </div>

              <div class="col-md-6">
                  <div class="card ">
                      <div class="card-body">
                          <h5 class="card-title">Streak Comparison</h5>
                          <canvas id="chartStreaks"></canvas>
                          <p class="card-text text-muted">This shows your current streaks vs your all time best.</p>
                      </div>
                  </div>
              </div>

              
              <div class="col-md-4">
                  <div class="card ">
                      <div class="card-body">
                          <h5 class="card-title">Habits By Daily, Weekly, Monthly</h5>
                          <canvas id="chartFreq"></canvas>
                          <p class="card-text text-muted">This shows how your habits are split by Daily, Weekly and Monthly.</p>
                      </div>
                  </div>
              </div>

              <div class="col-md-4">
                  <div class="card ">
                      <div class="card-body">
                          <h5 class="card-title">Tasks Done by Priority</h5>
                          <canvas id="chartPriority"></canvas>
                          <p class="card-text text-muted">This shows an overview of completed tasks by their priorities.</p>
                      </div>
                  </div>
              </div>

              
              <div class="col-md-4">
                  <div class="card ">
                      <div class="card-body">
                          <h5 class="card-title">Tasks To-Do</h5>
                          <canvas id="chartTaskToDo"></canvas>
                          <p class="card-text text-muted">This shows an overview of your incomplete tasks by priority.</p>
                      </div>
                  </div>
              </div>

        </div>

          
        </div> <!-- Main Content -->
    
      </div> <!-- col-9 -->
          
    </div> <!-- Main row -->
      
  </div> <!-- container-fluid -->


</body>


<?php
  
  // Get all tasks for this user - done and to be done
  $stmtTasks = $pdo->prepare("
    SELECT * FROM task WHERE user_id = ?
  ");
  
  $stmtTasks->execute(
    [$user["user_id"]]
  );
  
  $tasks = $stmtTasks->fetchAll(PDO::FETCH_ASSOC);

  $lowPriorityToDo = 0; 
  $mediumPriorityToDo = 0; 
  $highPriorityToDo = 0;
  
  $lowPriorityDone = 0; 
  $mediumPriorityDone = 0; 
  $highPriorityDone = 0;

  foreach ($tasks as $task) {
      if ($task['task_priority'] == 1) {
          if (empty($task["task_completeddate"])) $lowPriorityToDo = $lowPriorityToDo + 1;
          else $lowPriorityDone++;
      } 
      else if ($task['task_priority'] == 2) {
          if (empty($task["task_completeddate"])) $mediumPriorityToDo = $mediumPriorityToDo + 1;
          else $mediumPriorityDone++;
      } 
      else {
          if (empty($task["task_completeddate"])) $highPriorityToDo = $highPriorityToDo + 1;
          else $highPriorityDone++;
      }
  }

  // Get all habits for this user 
  $stmtStreaks = $pdo->prepare("
    SELECT * FROM habit WHERE user_id = ?
   ");
   
  $stmtStreaks->execute([
    $user["user_id"]
  ]);

  $habits = $stmtStreaks->fetchAll(PDO::FETCH_ASSOC);

  $habitNames = ""; 
  $currentStreaks = ""; 
  $bestStreaks = "";

  //create the chart data for current vs best so far
  foreach ($habits as $habit) {
      $habitNames = $habitNames . "'" . $habit['habit_name'] . "',";
      $currentStreaks = $currentStreaks . $habit['habit_currentstreak'] . ",";
      $bestStreaks = $bestStreaks . $habit['habit_beststreak'] . ",";
  }

  //create chart data of count of habits by daily, weekly, monthly
  $dailyCount = 0; 
  $weeklyCount = 0; 
  $monthlyCount = 0;

  foreach ($habits as $habit) {
      if ($habit['habit_frequency'] == 'Daily') {
          $dailyCount = $dailyCount + 1;
      }
      
      else if ($habit['habit_frequency'] == 'Weekly') {
          $weeklyCount = $weeklyCount + 1;
      }
      else if ($habit['habit_frequency'] == 'Monthly') {
        $monthlyCount = $monthlyCount + 1;
      }
  }

  // Habit consistency chart data over last 7 days 
  // For this get each day over the last 7 days and then check and count the number of habitlogs for each of those days
  
  $sevenDaysAgo = date('Y-m-d', strtotime('-7 days')); // gets the date from 7 days ago - and use 'Y-m-d' to match the database format like 2026-05-06
  
  //Get the habitlogs for the last days 
  $stmtLogs = $pdo->prepare("
      SELECT * FROM habitlog 
      WHERE user_id = ? AND habitlog_date >= ?
  ");

  
  $stmtLogs->execute([
      $user["user_id"], 
      $sevenDaysAgo
  ]);

  $habitlogs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

  // setup variables for each day starting from 7 days ago till today using strtotime and passing in how many days back to go
  $date1 = date('Y-m-d', strtotime("-6 days")); //eg 2026-05-13
  $dayName1 = date('D', strtotime("-6 days"));  //eg Monday
  $count1 = 0;
  
  $date2 = date('Y-m-d', strtotime("-5 days"));
  $dayName2 = date('D', strtotime("-5 days"));
  $count2 = 0;
  
  $date3 = date('Y-m-d', strtotime("-4 days"));
  $dayName3 = date('D', strtotime("-4 days"));
  $count3 = 0;
  
  $date4 = date('Y-m-d', strtotime("-3 days"));
  $dayName4 = date('D', strtotime("-3 days"));
  $count4 = 0;
  
  $date5 = date('Y-m-d', strtotime("-2 days"));
  $dayName5 = date('D', strtotime("-2 days"));
  $count5 = 0;
  
  $date6 = date('Y-m-d', strtotime("-1 days"));
  $dayName6 = date('D', strtotime("-1 days"));
  $count6 = 0;
  
  //todays date last so graph is from past till today
  $date7 = date('Y-m-d'); 
  $dayName7 = date('D');      
  $count7 = 0;
  
  foreach ($habitlogs as $habitlog) {
      if ($habitlog['habitlog_date'] == $date1) {
          $count1 = $count1 + 1;
      }
      else if ($habitlog['habitlog_date'] == $date2) {
          $count2 = $count2 + 1;
      }
      else if ($habitlog['habitlog_date'] == $date3) {
          $count3 = $count3 + 1;
      }
      else if ($habitlog['habitlog_date'] == $date4) {
          $count4 = $count4 + 1;
      }
      else if ($habitlog['habitlog_date'] == $date5) {
          $count5 = $count5 + 1;
      }
      else if ($habitlog['habitlog_date'] == $date6) {
          $count6 = $count6 + 1;
      }
      else if ($habitlog['habitlog_date'] == $date7) {
          $count7 = $count7 + 1;
      }

  }
  
  // creates a string like 'monday','tuesday',... for last 7 days.
  $habitlogDaysOfWeek = "";
  $habitlogDaysOfWeek = $habitlogDaysOfWeek . "'" . $dayName1 . "','" . $dayName2 . "','" . $dayName3 . "','" . $dayName4 . "','" . $dayName5 . "','" . $dayName6 . "','" . $dayName7 . "'";

  //creates a string for the count of habits logged per day like 1,3,5,7,8,9 for last 7 days
  $habitlogTotals= "";
  $habitlogTotals = $habitlogTotals . $count1 . "," . $count2 . "," . $count3 . "," . $count4 . "," . $count5 . "," . $count6 . "," . $count7;

?>

<script>

function loadCharts() { //called from the onload on body so will run when the page is loaded
    
    // Habit Weekly Consistency
    new Chart(document.getElementById('chartConsistency'), {
        type: 'line',
        data: {
            labels: [<?php echo $habitlogDaysOfWeek; ?>],  //eg ['Monday', 'Tuesday',....]  - the graps labels
            datasets: [{
                label: 'Habit Logs Per Day',
                data: [<?php echo $habitlogTotals; ?>],  //eg [1, 4, 6, 8, 1, 3] - the graph data
                tension: 0.3 //makes the graph smoother rather than jaggged lines
            }]
        }
    });

    // Streak Comparison
    new Chart(document.getElementById('chartStreaks'), {
        type: 'bar',
        data: {
            labels: [<?php echo $habitNames; ?>],
            datasets: [
                { label: 'Current Streak', data: [<?php echo $currentStreaks; ?>]},
                { label: 'Best Streak', data: [<?php echo $bestStreaks; ?>]}
            ]
        }
    });

    // Habit breakdown by frequency chart - use donught for variety
    new Chart(document.getElementById('chartFreq'), {
        type: 'doughnut',
        data: {
            labels: ['Daily', 'Weekly', 'Monthly'],
            datasets: [{
                data: [<?php echo $dailyCount; ?>, <?php echo $weeklyCount; ?>, <?php echo $monthlyCount; ?>]
            }]
        }
    });

    //  Tasks Done by Priority 
    new Chart(document.getElementById('chartPriority'), {
        type: 'pie',
        data: {
            labels: ['Low', 'Medium', 'High'],
            datasets: [{
                data: [<?php echo $lowPriorityDone; ?>, <?php echo $mediumPriorityDone; ?>, <?php echo $highPriorityDone; ?>],
                backgroundColor: ['green', 'orange', 'red']
            }]
        }
    });

    // Tasks To-Do by priority 
    new Chart(document.getElementById('chartTaskToDo'), {
        type: 'pie',
        data: {
            labels: ['Low', 'Medium', 'High'],
            datasets: [{
                data: [<?php echo $lowPriorityToDo; ?>, <?php echo $mediumPriorityToDo; ?>, <?php echo $highPriorityToDo; ?>],
                backgroundColor: ['green', 'orange', 'red']
            }]
        }
    });
    
}

</script>
</html>