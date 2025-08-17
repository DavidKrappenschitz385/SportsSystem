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

if (isset($_POST['addsport'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $name = $_POST['name'];
    $age_bracket = $_POST['age_bracket'];

    $input_error = array();
    if (empty($name)) {
        $input_error['name'] = "The Sport Name field is required.";
    }
    if (empty($age_bracket)) {
        $input_error['age_bracket'] = "The Age Bracket field is required.";
    }

    if (count($input_error) == 0) {
        $check_sport_stmt = $db_con->prepare("SELECT * FROM `sports` WHERE `name` = ?");
        $check_sport_stmt->bind_param("s", $name);
        $check_sport_stmt->execute();
        $check_sport_result = $check_sport_stmt->get_result();

        if ($check_sport_result->num_rows == 0) {
            $stmt = $db_con->prepare("INSERT INTO `sports`(`name`, `age_bracket`) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $age_bracket);

            if ($stmt->execute()) {
                $datainsert['insertsucess'] = '<p style="color: green;">Sport Added Successfully!</p>';
            } else {
                $datainsert['inserterror'] = '<p style="color: red;">Failed to Add Sport. Please check the information!</p>';
            }
            $stmt->close();
        } else {
            $datainsert['inserterror'] = '<p style="color: red;">This Sport already exists!</p>';
        }
        $check_sport_stmt->close();
    }
}
?>

<h1 class="text-primary"><i class="fas fa-plus"></i> Add a Sport <small class="text-warning">New Sport Entry</small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Sport</li>
    </ol>
</nav>

<div class="row">
    <div class="col-sm-6">
        <?php if (isset($datainsert)) { ?>
            <div role="alert" aria-live="assertive" aria-atomic="true" class="toast fade show" data-autohide="true" data-animation="true" data-delay="2000">
                <div class="toast-header">
                    <strong class="mr-auto">Sport Registration</strong>
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
                <label for="name">Sport Name</label>
                <input name="name" type="text" class="form-control" id="name" value="<?= isset($name) ? $name : ''; ?>" required>
                <?php if (isset($input_error['name'])) {
                    echo '<div class="alert alert-danger mt-2" role="alert">' . $input_error['name'] . '</div>';
                } ?>
            </div>

            <div class="form-group">
                <label for="age_bracket">Age Bracket</label>
                <input name="age_bracket" type="text" value="<?= isset($age_bracket) ? $age_bracket : ''; ?>" class="form-control" id="age_bracket" required>
                <?php if (isset($input_error['age_bracket'])) {
                    echo '<div class="alert alert-danger mt-2" role="alert">' . $input_error['age_bracket'] . '</div>';
                } ?>
            </div>

            <div class="form-group text-center">
                <input name="addsport" value="Add Sport" type="submit" class="btn btn-danger">
            </div>
        </form>
    </div>
</div>
