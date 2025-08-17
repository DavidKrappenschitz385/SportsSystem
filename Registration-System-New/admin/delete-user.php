<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('admin');

if (isset($_POST['delete_user'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $id = $_POST['id'];

    $stmt = $db_con->prepare("DELETE FROM `users` WHERE `id` = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        redirect('index.php?page=all-users&delete=success');
    } else {
        redirect('index.php?page=all-users&delete=error');
    }
    $stmt->close();
} else {
    redirect('index.php?page=all-users');
}
?>
