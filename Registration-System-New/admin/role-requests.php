<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('admin');

if (isset($_POST['approve'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }
    $id = $_POST['id'];
    $stmt = $db_con->prepare("UPDATE `users` SET `status` = 'approved' WHERE `id` = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    redirect('index.php?page=role-requests');
}

if (isset($_POST['decline'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }
    $id = $_POST['id'];
    $stmt = $db_con->prepare("UPDATE `users` SET `status` = 'declined' WHERE `id` = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    redirect('index.php?page=role-requests');
}
?>

<h1 class="text-primary"><i class="fas fa-user-check"></i> Role Requests</h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Role Requests</li>
    </ol>
</nav>

<table class="table table-striped table-hover table-bordered" id="data">
    <thead class="thead-dark">
        <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Username</th>
            <th>Requested Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $db_con->prepare("SELECT * FROM `users` WHERE `status` = 'pending'");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
        ?>
            <tr>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <form action="" method="POST" style="display: inline-block;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="approve" class="btn btn-success">Approve</button>
                    </form>
                    <form action="" method="POST" style="display: inline-block;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="decline" class="btn btn-danger">Decline</button>
                    </form>
                </td>
            </tr>
        <?php }
        $stmt->close(); ?>
    </tbody>
</table>
