<?php
if (isset($_POST['request_join_sport'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $sport_id = $_POST['sport_id'];
    $user_id = $user['id'];

    // Here you would typically insert a request into a database table,
    // for an admin to approve. For now, we'll just show a message.
    echo "<div class='alert alert-success'>Your request to join this sport has been sent to the administrator for approval.</div>";
}
?>
