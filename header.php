<?php
  // header.php
  // Ensure $pageTitle is set, otherwise use default
  
  if (!isset($pageTitle)) {
      $pageTitle = "Default Title"; 
  }

  // Include database connection
  require_once 'connection.php';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <link rel="stylesheet" href="/style.css"> //css file link

    //link bootstrap files-jquery
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>

</head>

<?php if (isset($ShowNavBar)) : ?>


<?php endif; ?>
