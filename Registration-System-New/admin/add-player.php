<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('admin');

$corepage = explode('/', $_SERVER['PHP_SELF']);
$corepage = end($corepage);
if ($corepage !== 'index.php') {
    if ($corepage == $corepage) {
        $corepage = explode('.', $corepage);
        header('Location: index.php?page=' . $corepage[0]);
    }
}

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

if (isset($_POST['addplayer'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $full_name = $_POST['full_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $sport_id = $_POST['sport_id'];
    $team_id = isset($_POST['team_id']) && !empty($_POST['team_id']) ? $_POST['team_id'] : null;
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $player_id_input = $_POST['player_id'];

    $input_error = array();
    if (empty($full_name)) {
        $input_error['full_name'] = "The Full Name field is required.";
    }
    if (empty($age)) {
        $input_error['age'] = "The Age field is required.";
    }
    if (empty($gender)) {
        $input_error['gender'] = "The Gender field is required.";
    }
    if (empty($sport_id)) {
        $input_error['sport_id'] = "The Sport field is required.";
    }
    if (empty($contact_number)) {
        $input_error['contact_number'] = "The Contact Number field is required.";
    }
    if (empty($address)) {
        $input_error['address'] = "The Address field is required.";
    }
    if (empty($_FILES['photo']['name'])) {
        $input_error['photo'] = "The ID Picture is required.";
    }

    if (count($input_error) == 0) {
        $check_player_stmt = $db_con->prepare("SELECT * FROM `players` WHERE `full_name` = ? AND `age` = ?");
        $check_player_stmt->bind_param("si", $full_name, $age);
        $check_player_stmt->execute();
        $check_player_result = $check_player_stmt->get_result();

        if ($check_player_result->num_rows == 0) {
            $photo = explode('.', $_FILES['photo']['name']);
            $photo_ext = end($photo);
            $photo_name = $player_id_input . date('YmdHis') . '.' . $photo_ext;

            $stmt = $db_con->prepare("INSERT INTO `players`(`player_id`, `full_name`, `age`, `gender`, `sport_id`, `team_id`, `contact_number`, `address`, `photo`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisissss", $player_id_input, $full_name, $age, $gender, $sport_id, $team_id, $contact_number, $address, $photo_name);

            if ($stmt->execute()) {
                $datainsert['insertsucess'] = '<p style="color: green;">Player Registered Successfully!</p>';
                move_uploaded_file($_FILES['photo']['tmp_name'], 'images/' . $photo_name);
            } else {
                $datainsert['inserterror'] = '<p style="color: red;">Registration Failed. Please check the information!</p>';
            }
            $stmt->close();
        } else {
            $datainsert['inserterror'] = '<p style="color: red;">This Player already exists!</p>';
        }
        $check_player_stmt->close();
    }
}
?>

<h1 class="text-primary"><i class="fas fa-user-plus"></i> Register a Player <small class="text-warning">New Player Entry</small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Player</li>
    </ol>
</nav>

<div class="row">
    <div class="col-sm-6">
        <?php if (isset($datainsert)) { ?>
            <div role="alert" aria-live="assertive" aria-atomic="true" class="toast fade show" data-autohide="true" data-animation="true" data-delay="2000">
                <div class="toast-header">
                    <strong class="mr-auto">Player Registration</strong>
                    <small><?php echo date('d-M-Y'); ?></small>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    <?php
                    if (isset($datainsert['insertsucess'])) {
                        echo $datainsert['insertsucess'];
                    }
                    if (isset($datainsert['inserterror'])) {
                        echo $datainsert['inserterror'];
                    }
                    ?>
                </div>
            </div>
        <?php } ?>

        <form enctype="multipart/form-data" method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input name="full_name" type="text" class="form-control" id="full_name" value="<?= isset($full_name) ? $full_name : ''; ?>" required>
                <?php if (isset($input_error['full_name'])) {
                    echo '<div class="alert alert-danger mt-2" role="alert">' . $input_error['full_name'] . '</div>';
                } ?>
            </div>

            <div class="form-group">
                <label for="player_id">Player ID (Auto-generated)</label>
                <input name="player_id" type="text" value="<?= $player_id; ?>" class="form-control" id="player_id" readonly required>
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input name="age" type="number" class="form-control" id="age" value="<?= isset($age) ? $age : ''; ?>" required>
                <?php if (isset($input_error['age'])) {
                    echo '<div class="alert alert-danger mt-2" role="alert">' . $input_error['age'] . '</div>';
                } ?>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select name="gender" class="form-control" id="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?= isset($gender) && $gender == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= isset($gender) && $gender == 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
                <?php if (isset($input_error['gender'])) {
                    echo '<div class="alert alert-danger mt-2" role="alert">' . $input_error['gender'] . '</div>';
                } ?>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input name="address" type="text" value="<?= isset($address) ? $address : ''; ?>" class="form-control" id="address" required>
                <?php if (isset($input_error['address'])) {
                    echo '<div class="alert alert-danger mt-2" role="alert">' . $input_error['address'] . '</div>';
                } ?>
            </div>

            <div class="form-group">
                <label for="contact_number">Contact #</label>
                <input name="contact_number" type="text" class="form-control" id="contact_number" value="<?= isset($contact_number) ? $contact_number : ''; ?>" pattern="09[0-9]{9}" placeholder="09XXXXXXXXX" required>
                <?php if (isset($input_error['contact_number'])) {
                    echo '<div class="alert alert-danger mt-2" role="alert">' . $input_error['contact_number'] . '</div>';
                } ?>
            </div>

            <div class="form-group">
                <label for="sport_id">Sport</label>
                <select name="sport_id" class="form-control" id="sport_id" required>
                    <option value="">Select Sport</option>
                    <?php
                    $sports_query = mysqli_query($db_con, "SELECT * FROM `sports` ORDER BY `name` ASC");
                    while ($sport = mysqli_fetch_assoc($sports_query)) {
                        $selected = isset($sport_id) && $sport_id == $sport['id'] ? 'selected' : '';
                        echo '<option value="' . $sport['id'] . '" ' . $selected . '>' . ucwords($sport['name']) . ' (' . $sport['age_bracket'] . ')</option>';
                    }
                    ?>
                </select>
                <?php if (isset($input_error['sport_id'])) {
                    echo '<div class="alert alert-danger mt-2" role="alert">' . $input_error['sport_id'] . '</div>';
                } ?>
            </div>

            <div class="form-group">
                <label for="team_id">Team (Optional)</label>
                <select name="team_id" class="form-control" id="team_id">
                    <option value="">Select Team</option>
                    <?php
                    $teams_query = mysqli_query($db_con, "SELECT * FROM `teams` ORDER BY `name` ASC");
                    while ($team = mysqli_fetch_assoc($teams_query)) {
                        $selected = isset($team_id) && $team_id == $team['id'] ? 'selected' : '';
                        echo '<option value="' . $team['id'] . '" ' . $selected . '>' . ucwords($team['name']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="photo">ID Picture</label>
                <input name="photo" type="file" class="form-control" id="photo" required>
                <?php if (isset($input_error['photo'])) {
                    echo '<div class="alert alert-danger mt-2" role="alert">' . $input_error['photo'] . '</div>';
                } ?>
            </div>

            <div class="form-group text-center">
                <input name="addplayer" value="Add Player" type="submit" class="btn btn-danger">
            </div>
        </form>
    </div>
</div>
