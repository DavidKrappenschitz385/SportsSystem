<?php
require_once 'includes/db_con.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

check_session();

$user = get_user_by_username($_SESSION['user_login']);
if ($user['role'] !== 'default') {
    redirect('profile.php');
}

if (isset($_POST['request_role'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $role = $_POST['role'];
    $username = $_SESSION['user_login'];

    $stmt = $db_con->prepare("UPDATE `users` SET `role` = ?, `status` = 'pending' WHERE `username` = ?");
    $stmt->bind_param("ss", $role, $username);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Your role request has been submitted. Please wait for admin approval.";
        redirect('profile.php');
    } else {
        $error = "Failed to submit role request. Please try again.";
    }
    $stmt->close();
}

$page_title = "Request Role";
require_once 'templates/header.php';
?>
<div class="container">
    <br>
    <h1 class="text-center">Request a Role</h1>
    <br>
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <?php if (isset($error)) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>
            <form method="POST" action="request-role.php">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="form-group">
                    <label for="role">Select Role</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="">Select a role</option>
                        <option value="player">Player</option>
                        <option value="coach">Coach</option>
                    </select>
                </div>
                <button type="submit" name="request_role" class="btn btn-primary">Submit Request</button>
                <a href="admin/logout.php" class="btn btn-secondary">Logout</a>
            </form>
        </div>
    </div>
</div>
<?php require_once 'templates/footer.php'; ?>
