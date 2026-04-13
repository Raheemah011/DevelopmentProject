<div class="col-11">
  <h2>Welcome <?php echo $user["user_name"]; ?></h2>
  <?php echo date("l, F j, Y"); ?>
</div>
<div class="col-1">
  <a href="#" class="btn btn-dark" onclick='$("html").attr("data-bs-theme", "dark")'>🌙</a>
  <a href="#" class="btn btn-light" onclick='$("html").attr("data-bs-theme", "light")'>☀️</a>                     
</div>
