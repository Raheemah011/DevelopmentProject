<?php
session_start(); //start session to access session variables

//check is theuser is logged in with session variables and see if it exists
if (!isset($_SESSION["user_email"])) {
    header("Location: index.php"); //if it isnt set go back to the log in page
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<!-- bootstrap for styling -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="container mt-5">
<!-- welcome message with users email -->
<h2>Welcome <?php echo $_SESSION["user_email"]; ?></h2>

<!--log out button -->
<a href="../account/logout.php" class="btn btn-danger mt-3">Logout</a>

</body>
</html>