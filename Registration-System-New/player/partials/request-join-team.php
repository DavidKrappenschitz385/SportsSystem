<?php
if (isset($_POST['request_join_team'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $team_id = $_POST['team_id'];
    $user_id = $user['id'];

    // Here you would typically insert a request into a database table,
    // for the coach to approve. For now, we'll just show a message.
    echo "<div class='alert alert-success'>Your request to join this team has been sent to the coach for approval.</div>";
}
?>
