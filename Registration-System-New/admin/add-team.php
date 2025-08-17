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

if (isset($_POST['addteam'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $name = $_POST['name'];
    $sport_id = $_POST['sport_id'];

    $input_error = array();
    if (empty($name)) {
        $input_error['name'] = "The Team Name field is required.";
    }
    if (empty($sport_id)) {
        $input_error['sport_id'] = "The Sport field is required.";
    }

    if (count($input_error) == 0) {
        $check_team_stmt = $db_con->prepare("SELECT * FROM `teams` WHERE `name` = ? AND `sport_id` = ?");
        $check_team_stmt->bind_param("si", $name, $sport_id);
        $check_team_stmt->execute();
        $check_team_result = $check_team_stmt->get_result();

        if ($check_team_result->num_rows == 0) {
            $stmt = $db_con->prepare("INSERT INTO `teams`(`name`, `sport_id`) VALUES (?, ?)");
            $stmt->bind_param("si", $name, $sport_id);

            if ($stmt->execute()) {
                $datainsert['insertsucess'] = '<p style="color: green;">Team Added Successfully!</p>';
            } else {
                $datainsert['inserterror'] = '<p style="color: red;">Failed to Add Team. Please check the information!</p>';
            }
            $stmt->close();
        } else {
            $datainsert['inserterror'] = '<p style="color: red;">This Team already exists in this sport!</p>';
        }
        $check_team_stmt->close();
    }
}
?>

<h1 class="text-primary"><i class="fas fa-users-cog"></i> Add a Team <small class="text-warning">New Team Entry</small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Team</li>
    </ol>
</nav>

<div class="row">
    <div class="col-sm-6">
        <?php if (isset($datainsert)) { ?>
            <div role="alert" aria-live="assertive" aria-atomic="true" class="toast fade show" data-autohide="true" data-animation="true" data-delay="2000">
                <div class="toast-header">
                    <strong class="mr-auto">Team Registration</strong>
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
                <label for="name">Team Name</label>
                <input name="name" type="text" class="form-control" id="name" value="<?= isset($name) ? $name : ''; ?>" required>
                <?php if (isset($input_error['name'])) {
                    echo '<div class="alert alert-danger mt-2" role="alert">' . $input_error['name'] . '</div>';
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

            <div class="form-group text-center">
                <input name="addteam" value="Add Team" type="submit" class="btn btn-danger">
            </div>
        </form>
    </div>
</div>
