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

$id = base64_decode($_GET['id']);
$oldPhoto = base64_decode($_GET['photo']);

if (isset($_POST['updateplayer'])) {
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

    if (!empty($_FILES['photo']['name'])) {
        $photo = $_FILES['photo']['name'];
        $photo_ext = pathinfo($photo, PATHINFO_EXTENSION);

        $player_id_stmt = $db_con->prepare("SELECT `player_id` FROM `players` WHERE `id` = ?");
        $player_id_stmt->bind_param("i", $id);
        $player_id_stmt->execute();
        $player_id_result = $player_id_stmt->get_result();
        $player_id_row = $player_id_result->fetch_assoc();
        $player_id = $player_id_row['player_id'];
        $photo_new = $player_id . date('YmdHis') . '.' . $photo_ext;
        $player_id_stmt->close();
    } else {
        $photo_new = $oldPhoto;
    }

    $stmt = $db_con->prepare("UPDATE `players` SET `full_name` = ?, `age` = ?, `gender` = ?, `sport_id` = ?, `team_id` = ?, `contact_number` = ?, `address` = ?, `photo` = ? WHERE `id` = ?");
    $stmt->bind_param("sisiisssi", $full_name, $age, $gender, $sport_id, $team_id, $contact_number, $address, $photo_new, $id);

    if ($stmt->execute()) {
        if (!empty($_FILES['photo']['name'])) {
            move_uploaded_file($_FILES['photo']['tmp_name'], 'images/' . $photo_new);
            if (file_exists('images/' . $oldPhoto)) {
                unlink('images/' . $oldPhoto);
            }
        }
        redirect('index.php?page=all-players&edit=success');
    } else {
        redirect('index.php?page=all-players&edit=error');
    }
    $stmt->close();
}
?>

<h1 class="text-primary"><i class="fas fa-user-edit"></i> Edit Player Information <small class="text-warning">Update Player</small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=all-players">All Players</a></li>
        <li class="breadcrumb-item active">Edit Player</li>
    </ol>
</nav>

<?php
if (isset($id)) {
    $stmt = $db_con->prepare("SELECT * FROM `players` WHERE `id` = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
}
?>

<div class="row">
    <div class="col-sm-6">
        <form enctype="multipart/form-data" method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input name="full_name" type="text" class="form-control" id="full_name" value="<?= $row['full_name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="player_id">Player ID</label>
                <input name="player_id" type="text" class="form-control" id="player_id" value="<?= $row['player_id']; ?>" readonly required>
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input name="age" type="number" class="form-control" id="age" value="<?= $row['age']; ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select name="gender" class="form-control" id="gender" required>
                    <option value="Male" <?= $row['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $row['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input name="address" type="text" class="form-control" id="address" value="<?= $row['address']; ?>" required>
            </div>

            <div class="form-group">
                <label for="contact_number">Contact #</label>
                <input name="contact_number" type="text" class="form-control" id="contact_number" value="<?= $row['contact_number']; ?>" pattern="09[0-9]{9}" required>
            </div>

            <div class="form-group">
                <label for="sport_id">Sport</label>
                <select name="sport_id" class="form-control" id="sport_id" required>
                    <?php
                    $sports_query = mysqli_query($db_con, "SELECT * FROM `sports` ORDER BY `name` ASC");
                    while ($sport = mysqli_fetch_assoc($sports_query)) {
                        $selected = ($sport['id'] == $row['sport_id']) ? 'selected' : '';
                        echo '<option value="' . $sport['id'] . '" ' . $selected . '>' . ucwords($sport['name']) . ' (' . $sport['age_bracket'] . ')</option>';
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
                        $selected = ($team['id'] == $row['team_id']) ? 'selected' : '';
                        echo '<option value="' . $team['id'] . '" ' . $selected . '>' . ucwords($team['name']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <?php if (!empty($row['photo'])) : ?>
                <div class="form-group">
                    <label>Current ID Picture:</label><br>
                    <img src="images/<?= $row['photo']; ?>" alt="ID Picture" style="max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="photo">Update ID Picture (Optional)</label>
                <input name="photo" type="file" class="form-control" id="photo">
            </div>

            <div class="form-group text-center">
                <input name="updateplayer" value="Update Player" type="submit" class="btn btn-danger">
            </div>
        </form>
    </div>
</div>
