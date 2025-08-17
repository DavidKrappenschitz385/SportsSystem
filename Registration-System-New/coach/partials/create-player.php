<?php
if (isset($_POST['create_player'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $full_name = $_POST['full_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $sport_id = $_POST['sport_id'];
    $team_id = $_POST['team_id'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    // Generate unique Player ID
    $player_id = 'P' . rand(100000, 999999);
    $check_stmt = $db_con->prepare("SELECT `player_id` FROM `players` WHERE `player_id` = ?");
    $check_stmt->bind_param("s", $player_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    while ($check_result->num_rows > 0) {
        $player_id = 'P' . rand(100000, 999999);
        $check_stmt->bind_param("s", $player_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
    }
    $check_stmt->close();

    $photo_name = '';
    if (!empty($_FILES['photo']['name'])) {
        $photo = explode('.', $_FILES['photo']['name']);
        $photo_ext = end($photo);
        $photo_name = $player_id . date('YmdHis') . '.' . $photo_ext;
    }

    $stmt = $db_con->prepare("INSERT INTO `players`(`player_id`, `full_name`, `age`, `gender`, `sport_id`, `team_id`, `contact_number`, `address`, `photo`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisiisss", $player_id, $full_name, $age, $gender, $sport_id, $team_id, $contact_number, $address, $photo_name);

    if ($stmt->execute()) {
        if (!empty($photo_name)) {
            move_uploaded_file($_FILES['photo']['tmp_name'], '../admin/images/' . $photo_name);
        }
        echo "<div class='alert alert-success'>Player created successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to create player.</div>";
    }
    $stmt->close();
}
?>

<h2 class="text-primary">Create a New Player</h2>
<form method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <div class="form-group">
        <label for="full_name">Full Name</label>
        <input name="full_name" type="text" class="form-control" id="full_name" required>
    </div>
    <div class="form-group">
        <label for="age">Age</label>
        <input name="age" type="number" class="form-control" id="age" required>
    </div>
    <div class="form-group">
        <label for="gender">Gender</label>
        <select name="gender" class="form-control" id="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
    </div>
    <div class="form-group">
        <label for="address">Address</label>
        <input name="address" type="text" class="form-control" id="address" required>
    </div>
    <div class="form-group">
        <label for="contact_number">Contact #</label>
        <input name="contact_number" type="text" class="form-control" id="contact_number" pattern="09[0-9]{9}" placeholder="09XXXXXXXXX" required>
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
    <div class="form-group">
        <label for="team_id">Team</label>
        <select name="team_id" class="form-control" id="team_id" required>
            <option value="">Select Team</option>
            <?php
            $coach_id = $user['id'];
            $teams_query = mysqli_query($db_con, "SELECT t.* FROM `teams` t JOIN `coaches` c ON t.id = c.team_id WHERE c.user_id = $coach_id ORDER BY t.`name` ASC");
            while ($team = mysqli_fetch_assoc($teams_query)) {
                echo '<option value="' . $team['id'] . '">' . ucwords($team['name']) . '</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <label for="photo">ID Picture</label>
        <input name="photo" type="file" class="form-control" id="photo" required>
    </div>
    <div class="form-group text-center">
        <input name="create_player" value="Create Player" type="submit" class="btn btn-primary">
    </div>
</form>
