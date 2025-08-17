<?php
require_once 'includes/db_con.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

if (isset($_POST['register'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $selected_role = $_POST['selected_role'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db_con->prepare("INSERT INTO `users` (`full_name`, `email`, `username`, `password`, `role`, `status`) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("sssss", $full_name, $email, $username, $hashed_password, $selected_role);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        if ($selected_role === 'player') {
            $player_id = 'P' . rand(100000, 999999);
            // Ensure player_id is unique
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

            $age = $_POST['age'] ?? null;
            $gender = $_POST['gender'] ?? '';
            $sport_id = $_POST['sport_id'] ?? '';
            $team_id = !empty($_POST['team_id']) ? $_POST['team_id'] : null;
            $contact_number = $_POST['contact_number'] ?? '';
            $address = $_POST['address'] ?? '';
            $photo_name = '';
            if (!empty($_FILES['photo']['name'])) {
                $photo = explode('.', $_FILES['photo']['name']);
                $photo_ext = end($photo);
                $photo_name = $player_id . date('YmdHis') . '.' . $photo_ext;
            }

            $insert_player_stmt = $db_con->prepare("INSERT INTO `players`(`user_id`, `player_id`, `full_name`, `age`, `gender`, `sport_id`, `team_id`, `contact_number`, `address`, `photo`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_player_stmt->bind_param("issisissss", $user_id, $player_id, $full_name, $age, $gender, $sport_id, $team_id, $contact_number, $address, $photo_name);
            $insert_player_stmt->execute();
            $insert_player_stmt->close();

            if (!empty($photo_name)) {
                move_uploaded_file($_FILES['photo']['tmp_name'], 'admin/images/' . $photo_name);
            }
        } elseif ($selected_role === 'coach') {
            $experience_years = $_POST['experience_years'] ?? 0;
            $certifications = $_POST['certifications'] ?? '';
            $sport_id = $_POST['coach_sport_id'] ?? '';
            $team_id = !empty($_POST['coach_team_id']) ? $_POST['coach_team_id'] : null;

            $insert_coach_stmt = $db_con->prepare("INSERT INTO `coaches`(`user_id`, `full_name`, `experience_years`, `certifications`, `sport_id`, `team_id`) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_coach_stmt->bind_param("isissi", $user_id, $full_name, $experience_years, $certifications, $sport_id, $team_id);
            $insert_coach_stmt->execute();
            $insert_coach_stmt->close();
        }
        $_SESSION['message'] = "Registration successful! You can log in now. Approval is pending.";
        redirect('admin/login.php');
    } else {
        $error = "Registration failed. Please try again.";
    }
    $stmt->close();
}

$page_title = "User Registration";
require_once 'templates/header.php';
?>
<div class="container">
    <br>
    <h1 class="text-center">User Registration</h1>
    <br>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <?php if (isset($error)) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>
            <form method="POST" action="register.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="selected_role">Register as</label>
                    <select name="selected_role" id="selected_role" class="form-control" required>
                        <option value="">Select an option</option>
                        <option value="player">Player</option>
                        <option value="coach">Coach</option>
                    </select>
                </div>

                <div id="player_fields" style="display:none;">
                    <hr>
                    <h5>Player Details</h5>
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
                                echo '<option value="'.$sport['id'].'">'.ucwords($sport['name']).' ('.$sport['age_bracket'].')</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="team_id">Team (Optional)</label>
                        <select name="team_id" class="form-control" id="team_id">
                            <option value="">Select Team</option>
                            <?php
                            $teams_query = mysqli_query($db_con, "SELECT * FROM `teams` ORDER BY `name` ASC");
                            while ($team = mysqli_fetch_assoc($teams_query)) {
                                echo '<option value="'.$team['id'].'">'.ucwords($team['name']).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="photo">ID Picture</label>
                        <input name="photo" type="file" class="form-control" id="photo" required>
                    </div>
                </div>

                <div id="coach_fields" style="display:none;">
                    <hr>
                    <h5>Coach Details</h5>
                    <div class="form-group">
                        <label for="experience_years">Years of Experience</label>
                        <input name="experience_years" type="number" min="0" class="form-control" id="experience_years">
                    </div>
                    <div class="form-group">
                        <label for="certifications">Certifications</label>
                        <textarea name="certifications" class="form-control" id="certifications" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="coach_sport_id">Sport</label>
                        <select name="coach_sport_id" class="form-control" id="coach_sport_id" required>
                            <option value="">Select Sport</option>
                            <?php
                            $sports_query2 = mysqli_query($db_con, "SELECT * FROM `sports` ORDER BY `name` ASC");
                            while ($sport2 = mysqli_fetch_assoc($sports_query2)) {
                                echo '<option value="'.$sport2['id'].'">'.ucwords($sport2['name']).' ('.$sport2['age_bracket'].')</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="coach_team_id">Team (Optional)</label>
                        <select name="coach_team_id" class="form-control" id="coach_team_id">
                            <option value="">Select Team</option>
                            <?php
                            $teams_query2 = mysqli_query($db_con, "SELECT * FROM `teams` ORDER BY `name` ASC");
                            while ($team2 = mysqli_fetch_assoc($teams_query2)) {
                                echo '<option value="'.$team2['id'].'">'.ucwords($team2['name']).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <button type="submit" name="register" class="btn btn-primary">Register</button>
                <a href="index.php" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
<script>
    (function(){
        var select = document.getElementById('selected_role');
        var playerFieldsContainer = document.getElementById('player_fields');
        var coachFieldsContainer = document.getElementById('coach_fields');

        var playerFields = playerFieldsContainer.querySelectorAll('input, select');
        var coachFields = coachFieldsContainer.querySelectorAll('input, select, textarea');

        function setRequired(elements, isRequired) {
            elements.forEach(function(el) {
                // team_id is optional for players, and several fields are optional for coaches.
                if (el.name === 'team_id' || el.name === 'coach_team_id' || el.name === 'experience_years' || el.name === 'certifications') {
                    // Do not make these fields required
                } else {
                    el.required = isRequired;
                }
            });
        }

        function toggle(){
            var v = select.value;
            if (v === 'player') {
                playerFieldsContainer.style.display = 'block';
                coachFieldsContainer.style.display = 'none';
                setRequired(playerFields, true);
                setRequired(coachFields, false);
            } else if (v === 'coach') {
                playerFieldsContainer.style.display = 'none';
                coachFieldsContainer.style.display = 'block';
                setRequired(playerFields, false);
                setRequired(coachFields, true);
            } else {
                playerFieldsContainer.style.display = 'none';
                coachFieldsContainer.style.display = 'none';
                setRequired(playerFields, false);
                setRequired(coachFields, false);
            }
        }

        select && select.addEventListener('change', toggle);
        toggle(); // run on page load to set initial state
    })();
</script>
