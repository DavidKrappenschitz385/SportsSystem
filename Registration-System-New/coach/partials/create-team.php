<?php
if (isset($_POST['create_team'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $name = $_POST['name'];
    $sport_id = $_POST['sport_id'];
    $coach_id = $user['id'];

    $stmt = $db_con->prepare("INSERT INTO `teams` (`name`, `sport_id`) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $sport_id);
    if ($stmt->execute()) {
        $team_id = $stmt->insert_id;
        $stmt->close();

        // Associate the coach with the new team
        $update_coach_stmt = $db_con->prepare("UPDATE `coaches` SET `team_id` = ? WHERE `user_id` = ?");
        $update_coach_stmt->bind_param("ii", $team_id, $coach_id);
        $update_coach_stmt->execute();
        $update_coach_stmt->close();

        echo "<div class='alert alert-success'>Team created successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to create team.</div>";
    }
}
?>

<h2 class="text-primary">Create a New Team</h2>
<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <div class="form-group">
        <label for="name">Team Name</label>
        <input name="name" type="text" class="form-control" id="name" required>
    </div>
    <div class="form-group">
        <label for="sport_id">Sport</label>
        <select name="sport_id" class="form-control" id="sport_id" required>
            <option value="">Select Sport</option>
            <?php
            $sports_query = mysqli_query($db_con, "SELECT * FROM `sports` ORDER BY `name` ASC");
            while ($sport = mysqli_fetch_assoc($sports_query)) {
                echo '<option value="' . $sport['id'] . '">' . ucwords($sport['name']) . ' (' . $sport['age_bracket'] . ')</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-group text-center">
        <input name="create_team" value="Create Team" type="submit" class="btn btn-primary">
    </div>
</form>
