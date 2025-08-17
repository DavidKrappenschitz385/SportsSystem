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

if (isset($_POST['addcoach'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $full_name = $_POST['full_name'];
    $experience_years = $_POST['experience_years'];
    $certifications = $_POST['certifications'];
    $sport_id = $_POST['sport_id'];
    $team_id = isset($_POST['team_id']) && !empty($_POST['team_id']) ? $_POST['team_id'] : null;

    $input_error = array();
    if (empty($full_name)) {
        $input_error['full_name'] = "The Full Name field is required.";
    }
    if (empty($sport_id)) {
        $input_error['sport_id'] = "The Sport field is required.";
    }

    if (count($input_error) == 0) {
        $stmt = $db_con->prepare("INSERT INTO `coaches`(`full_name`, `experience_years`, `certifications`, `sport_id`, `team_id`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisii", $full_name, $experience_years, $certifications, $sport_id, $team_id);
        if ($stmt->execute()) {
            $datainsert['insertsucess'] = '<p style="color: green;">Coach Registered Successfully!</p>';
        } else {
            $datainsert['inserterror'] = '<p style="color: red;">Registration Failed. Please check the information!</p>';
        }
        $stmt->close();
    }
}
?>

<h1 class="text-primary"><i class="fa fa-chalkboard-teacher"></i> Register a Coach <small class="text-warning">New Coach Entry</small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Coach</li>
    </ol>
</nav>

<div class="row">
    <div class="col-sm-8">
        <?php if (isset($datainsert)) { ?>
            <div role="alert" aria-live="assertive" aria-atomic="true" class="toast fade show" data-autohide="true" data-animation="true" data-delay="2000">
                <div class="toast-header">
                    <strong class="mr-auto">Coach Registration</strong>
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

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input name="full_name" type="text" class="form-control" id="full_name" value="<?= isset($full_name) ? $full_name : ''; ?>" required>
                <?php if (isset($input_error['full_name'])) {
                    echo '<div class="alert alert-danger mt-2" role="alert">' . $input_error['full_name'] . '</div>';
                } ?>
            </div>

            <div class="form-group">
                <label for="experience_years">Years of Experience</label>
                <input name="experience_years" type="number" min="0" class="form-control" id="experience_years" value="<?= isset($experience_years) ? $experience_years : '0'; ?>">
            </div>

            <div class="form-group">
                <label for="certifications">Certifications</label>
                <textarea name="certifications" class="form-control" id="certifications" rows="2"><?= isset($certifications) ? htmlspecialchars($certifications) : ''; ?></textarea>
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
                        echo '<option value="' . $team['id'] . '">' . ucwords($team['name']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group text-center">
                <input name="addcoach" value="Add Coach" type="submit" class="btn btn-info">
            </div>
        </form>
    </div>
</div>