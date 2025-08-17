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

if (isset($_POST['updatesport'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $name = $_POST['name'];
    $age_bracket = $_POST['age_bracket'];

    $stmt = $db_con->prepare("UPDATE `sports` SET `name` = ?, `age_bracket` = ? WHERE `id` = ?");
    $stmt->bind_param("ssi", $name, $age_bracket, $id);

    if ($stmt->execute()) {
        redirect('index.php?page=all-sports&edit=success');
    } else {
        redirect('index.php?page=all-sports&edit=error');
    }
    $stmt->close();
}
?>

<h1 class="text-primary"><i class="fas fa-edit"></i> Edit Sport Information <small class="text-warning">Update Sport</small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=all-sports">All Sports</a></li>
        <li class="breadcrumb-item active">Edit Sport</li>
    </ol>
</nav>

<?php
if (isset($id)) {
    $stmt = $db_con->prepare("SELECT `id`, `name`, `age_bracket` FROM `sports` WHERE `id` = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
}
?>

<div class="row">
    <div class="col-sm-6">
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="form-group">
                <label for="name">Sport Name</label>
                <input name="name" type="text" class="form-control" id="name" value="<?= $row['name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="age_bracket">Age Bracket</label>
                <input name="age_bracket" type="text" class="form-control" id="age_bracket" value="<?= $row['age_bracket']; ?>" required>
            </div>

            <div class="form-group text-center">
                <input name="updatesport" value="Update Sport" type="submit" class="btn btn-danger">
            </div>
        </form>
    </div>
</div>
