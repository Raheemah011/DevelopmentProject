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
    $pdo->exec("DROP TABLE IF EXISTS user");

    //create the user table if it didnt exist
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
    $sqlResult =  "Table created successfully";
} catch (PDOException $e) {
    $sqlResult = "Error: " . $e->getMessage();
}

?>

<div class="alert alert-info">
<?php echo $sqlResult; ?>
</div>

</body>
