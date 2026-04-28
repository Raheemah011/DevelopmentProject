<?php
$pageTitle = "RunSql";
include "header.php";
?>

<body>
<h1>SQL Statements</h1>

<?php

$sqlResult = ""; //stores success or error messages

try {

//delete tthe user table if it already exists reset the database when needed
    //$pdo->exec("DROP TABLE IF EXISTS user");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user (
          user_id INT AUTO_INCREMENT PRIMARY KEY,
          user_name VARCHAR(255) NOT NULL,
          user_email VARCHAR(255) UNIQUE NOT NULL,
          user_password VARCHAR(255) NOT NULL,
          user_dob DATE NOT NULL,
          user_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
//display mesaage if works
    $sqlResult =  "<br>User table created successfully";
} catch (PDOException $e) {
    $sqlResult = "User Error: " . $e->getMessage();
}

try { //create tasks table and delete if it already exists
  
    $pdo->exec("DROP TABLE IF EXISTS task");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS task (
          user_id INT NOT NULL,
          task_id INT AUTO_INCREMENT PRIMARY KEY,
          task_name VARCHAR(255) NOT NULL,
          task_description VARCHAR(255),
          task_priority INTEGER NOT NULL,
          task_duedate DATE NOT NULL,
          task_completeddate DATE
        )
    ");

    //thsi will create me test data
    $pdo->exec("
        INSERT INTO task (user_id, task_id, task_name, task_description, task_priority, task_duedate, task_completeddate) VALUES
        (1, 1, 'Buy groceries', 'Milk, bread, eggs', 2, '2026-04-22', NULL),
        (1, 2, 'Finish report', 'Complete monthly report for work', 1, '2026-04-23', NULL),
        (1, 3, 'Gym session', 'Leg day workout', 3, '2026-04-21', '2026-04-21'),
        (1, 4, 'Call plumber', 'Fix kitchen sink leak', 2, '2026-04-25', NULL),
        (1, 5, 'Read book', 'Read 20 pages of novel', 3, '2026-04-20', '2026-04-20'),
        (1, 6, 'Pay bills', 'Electricity and internet', 1, '2026-04-18', '2026-04-18'),
        (1, 7, 'Clean house', 'Full house cleaning', 2, '2026-04-24', NULL),
        (1, 8, 'Study PHP', 'Practice PDO and MySQL queries', 1, '2026-04-26', NULL),
        (1, 9, 'Doctor appointment', 'Routine check-up', 1, '2026-04-27', NULL),
        (1, 10, 'Plan weekend', 'Organise activities', 3, '2026-04-19', '2026-04-19')
    ");

    $sqlResult =  $sqlResult . "<br>Task Table created successfully";
} catch (PDOException $e) {
    $sqlResult = $sqlResult . "<br>Task Error: " . $e->getMessage();
}

try { //create habit table and delete if it exists

    $pdo->exec("DROP TABLE IF EXISTS habit");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS habit (
          user_id INT NOT NULL,
          habit_id INT AUTO_INCREMENT PRIMARY KEY,
          habit_name VARCHAR(255) NOT NULL,
          habit_description VARCHAR(255),
          habit_frequency VARCHAR(12),
          habit_beststreak INT,
          habit_currentstreak INT
        )
    ");

    $sqlResult =  $sqlResult . "<br>Habit Table created successfully";
} catch (PDOException $e) {
    $sqlResult = $sqlResult . "<br>Habit Error: " . $e->getMessage();
}

try { //create habit table and delete if it exists

    $pdo->exec("DROP TABLE IF EXISTS habitlog");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS habitlog (
          habitlog_id INT AUTO_INCREMENT PRIMARY KEY,
          user_id INT NOT NULL,
          habit_id INT NOT NULL,
          habitlog_date Date NOT NULL,
          habitlog_yearweek  INT NOT NULL,
          habitlog_yearmonth INT NOT NULL
        )
    ");

    $sqlResult =  $sqlResult . "<br>Habitlog Table created successfully";
} catch (PDOException $e) {
    $sqlResult = $sqlResult . "<br>Habitlog Error: " . $e->getMessage();
}

try { //create habit table and delete if it exists

    //this will create me test data
    $pdo->exec("
        INSERT INTO habit (user_id, habit_id, habit_name, habit_description, habit_frequency, habit_beststreak, habit_currentstreak) VALUES
        (1, 1, 'Drink Water', 'Drink 2L of water', 'Daily', 5, 3),
        (1, 2, 'Workout', '30 mins exercise', 'Daily', 10, 2),
        (1, 3, 'Read Book', 'Read 10 pages', 'Daily', 7, 0),
        (1, 4, 'Meditate', '10 mins meditation', 'Daily', 4, 1),
        (1, 5, 'Walk Dog', 'Evening walk', 'Daily', 8, 4),
        (1, 6, 'Plan Week', 'Plan tasks for the week', 'Weekly', 6, 2),
        (1, 7, 'Call Family', 'Catch up with family', 'Weekly', 12, 5),
        (1, 8, 'Clean House', 'Full house clean', 'Weekly', 4, 1),
        (1, 9, 'Review Goals', 'Review monthly goals', 'Monthly', 3, 1),
        (1, 10, 'Budget Review', 'Check finances and spending', 'Monthly', 8, 2),
        (1, 11, 'Deep Clean', 'Deep clean entire home', 'Monthly', 4, 4)
    ");

    $sqlResult =  $sqlResult . "<br>Habit data created successfully";
} catch (PDOException $e) {
    $sqlResult = $sqlResult . "<br>Habitlog Error: " . $e->getMessage();
}


try { //create habit table and delete if it exists

    //this will create me test data
    $pdo->exec("
      INSERT INTO habitlog (habitlog_id, user_id, habit_id, habitlog_date, habitlog_yearweek, habitlog_yearmonth) VALUES
      (1, 1, 1, '2026-04-20', 202617, 202604),
      (2, 1, 1, '2026-04-21', 202617, 202604),
      (3, 1, 1, '2026-04-22', 202617, 202604),

      (4, 1, 2, '2026-04-18', 202616, 202604),
      (5, 1, 2, '2026-04-20', 202617, 202604),

      (6, 1, 3, '2026-04-15', 202616, 202604),

      (7, 1, 4, '2026-04-21', 202617, 202604),

      (8, 1, 5, '2026-04-19', 202616, 202604),
      (9, 1, 5, '2026-04-20', 202617, 202604),
      (10, 1, 5, '2026-04-21', 202617, 202604),
      (11, 1, 5, '2026-04-22', 202617, 202604),
      (12, 1, 6, '2026-04-06', 202615, 202604),
      (13, 1, 6, '2026-04-13', 202616, 202604),
      (14, 1, 6, '2026-04-20', 202617, 202604),

      (15, 1, 7, '2026-03-30', 202614, 202603),
      (16, 1, 7, '2026-04-06', 202615, 202604),
      (17, 1, 7, '2026-04-13', 202616, 202604),
      (18, 1, 7, '2026-04-20', 202617, 202604),

      (19, 1, 8, '2026-04-06', 202615, 202604),
      (20, 1, 8, '2026-04-20', 202617, 202604),

      (21, 1, 9, '2026-02-01', 202605, 202602),
      (22, 1, 9, '2026-03-01', 202609, 202603),
      (23, 1, 9, '2026-04-01', 202614, 202604),

      (24, 1, 10, '2026-03-05', 202610, 202603),
      (25, 1, 10, '2026-04-05', 202615, 202604),

      (26, 1, 11, '2026-01-10', 202601, 202601),      
      (27, 1, 11, '2026-02-10', 202605, 202602),      
      (28, 1, 11, '2026-03-10', 202611, 202603),      
      (29, 1, 11, '2026-04-10', 202617, 202604)      
   ");

    $sqlResult =  $sqlResult . "<br>Habitlog data created successfully";
} catch (PDOException $e) {
    $sqlResult = $sqlResult . "<br>Habitlog Error: " . $e->getMessage();
}


?>

<div class="alert alert-info">
<?php echo $sqlResult; ?>
</div>

<a href="dashboard.php">DashBoard</a>


</body>