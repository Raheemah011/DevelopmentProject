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
  
    //$pdo->exec("DROP TABLE IF EXISTS task");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS task (
          user_id INT NOT NULL,
          task_id INT AUTO_INCREMENT PRIMARY KEY,
          task_name VARCHAR(255) NOT NULL,
          task_description VARCHAR(255),
          task_priority INTEGER NOT NULL,
          task_duedate DATE NOT NULL,
          task_completeddate DATE,
        )
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
          habitlog_date Date NOT NULL
        )
    ");

    $sqlResult =  $sqlResult . "<br>Habitlog Table created successfully";
} catch (PDOException $e) {
    $sqlResult = $sqlResult . "<br>Habitlog Error: " . $e->getMessage();
}



?>

<div class="alert alert-info">
<?php echo $sqlResult; ?>
</div>

<a href="dashboard.php">DashBoard</a>


</body>