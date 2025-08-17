<?php
if (isset($_POST['invite_player'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $player_id = $_POST['player_id'];
    $coach_id = $user['id'];

    // Here you would typically insert an invitation into a database table,
    // for the player to approve. For now, we'll just show a message.
    echo "<div class='alert alert-success'>An invitation has been sent to the player.</div>";
}
?>
