<?php
$page_title = "Unauthorized";
require_once 'templates/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <h1 class="display-1">403</h1>
            <h2>Unauthorized Access</h2>
            <p>Sorry, you don't have permission to access this page.</p>
            <p><a href="javascript:history.back()">Go Back</a> or <a href="index.php">Go to Homepage</a></p>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
