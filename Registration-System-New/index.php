<?php
require_once 'includes/db_con.php';
$page_title = "Sports Registration System";
require_once 'templates/header.php';
?>
<div class="container"><br>
    <a class="btn btn-primary float-right" href="admin/login.php">Login</a>
    <a class="btn btn-success float-right" href="register.php" style="margin-right: 10px;">Register</a>
    <br>
    <br>
    <h1 class="text-center">Welcome to the Sports Registration System!</h1><br>
</div>
<?php require_once 'templates/footer.php'; ?>