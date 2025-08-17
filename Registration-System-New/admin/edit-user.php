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

if (isset($_POST['userupdate'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $stmt = $db_con->prepare("UPDATE `users` SET `full_name` = ?, `email` = ?, `role` = ?, `status` = ? WHERE `id` = ?");
    $stmt->bind_param("ssssi", $full_name, $email, $role, $status, $id);

    if ($stmt->execute()) {
        redirect('index.php?page=all-users&edit=success');
    } else {
        redirect('index.php?page=all-users&edit=error');
    }
    $stmt->close();
}
?>
<h1 class="text-primary"><i class="fas fa-user-edit"></i> Edit User Information<small class="text-warning"> Update User</small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard </a></li>
        <li class="breadcrumb-item" aria-current="page"><a href="index.php?page=all-users">All Users </a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit User</li>
    </ol>
</nav>

<?php
if (isset($id)) {
    $stmt = $db_con->prepare("SELECT `full_name`, `email`, `role`, `status` FROM `users` WHERE `id` = ?");
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
                <input name="full_name" type="text" class="form-control" id="full_name" value="<?php echo $row['full_name']; ?>" required="">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input name="email" type="email" class="form-control" id="email" value="<?php echo $row['email']; ?>" required="">
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                    <option value="coach" <?php if ($row['role'] == 'coach') echo 'selected'; ?>>Coach</option>
                    <option value="player" <?php if ($row['role'] == 'player') echo 'selected'; ?>>Player</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                    <option value="approved" <?php if ($row['status'] == 'approved') echo 'selected'; ?>>Approved</option>
                    <option value="declined" <?php if ($row['status'] == 'declined') echo 'selected'; ?>>Declined</option>
                </select>
            </div>

            <div class="form-group text-center">
                <input name="userupdate" value="Update User" type="submit" class="btn btn-danger">
            </div>
        </form>
    </div>
</div>