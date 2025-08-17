<?php
require_once 'includes/db_con.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

check_session();

$user = get_user_by_username($_SESSION['user_login']);
if (!$user) {
    session_destroy();
    redirect('admin/login.php');
}

$page_title = "My Profile";
require_once 'templates/header.php';
?>
<div class="container mt-4">
    <div class="d-flex justify-content-end mb-3">
        <a class="btn btn-secondary mr-2" href="index.php">Home</a>
        <a class="btn btn-danger" href="admin/logout.php">Logout</a>
    </div>
    <h2 class="mb-4">My Profile</h2>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Role:</strong> <?php echo htmlspecialchars(ucfirst($user['role'])); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($user['status'])); ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if ($user['role'] === 'default') { ?>
        <div class="alert alert-info">You have not selected a role yet. Please request a role to proceed.</div>
        <a href="request-role.php" class="btn btn-primary">Request Role</a>
    <?php } elseif ($user['status'] !== 'approved') { ?>
        <div class="alert alert-warning">Your application is currently pending approval. You can view your profile and status here. Other functionality is disabled until approval.</div>
    <?php } else { ?>
        <div class="card mt-4">
            <div class="card-header">
                <h4><?php echo htmlspecialchars(ucfirst($user['role'])); ?> Dashboard</h4>
            </div>
            <div class="card-body">
                <p>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>! Your account is approved. Here are your available actions:</p>
                <a href="<?php echo $user['role']; ?>/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
            </div>
        </div>
    <?php } ?>
</div>
<?php require_once 'templates/footer.php'; ?>