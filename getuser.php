<?php


  if (!isset($_SESSION["user_email"])) {
      header("Location: index.php");
      exit;
  }

  $stmt = $pdo->prepare(" 
      SELECT * FROM user where user_email = ?
  ");

  //execute the query
  $stmt->execute([
      $_SESSION["user_email"]
  ]);
    
  $user = $stmt->fetch(PDO::FETCH_ASSOC); //this gets the user record from the database

?>