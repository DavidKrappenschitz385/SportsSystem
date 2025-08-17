<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('player');

$page_title = "Player Dashboard";
require_once '../templates/header.php';

$user = get_user_by_username($_SESSION['user_login']);
?>

<div class="container-fluid player-dashboard">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="dashboard.php?page=main" class="list-group-item list-group-item-action active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="dashboard.php?page=view-sports" class="list-group-item list-group-item-action"><i class="fas fa-volleyball-ball"></i> View Sports</a>
                <a href="dashboard.php?page=view-teams" class="list-group-item list-group-item-action"><i class="fas fa-users"></i> View Teams</a>
                <a href="dashboard.php?page=view-players" class="list-group-item list-group-item-action"><i class="fas fa-users"></i> View Players</a>
                <a href="dashboard.php?page=view-coaches" class="list-group-item list-group-item-action"><i class="fas fa-chalkboard-teacher"></i> View Coaches</a>
                <a href="../profile.php" class="list-group-item list-group-item-action"><i class="fas fa-user"></i> My Profile</a>
                <a href="../admin/logout.php" class="list-group-item list-group-item-action"><i class="fas fa-power-off"></i> Logout</a>
            </div>
        </div>
        <div class="col-md-9">
            <div class="content">
                <?php
                $page = $_GET['page'] ?? 'main';
                $allowed_pages = ['main', 'view-sports', 'view-teams', 'view-players', 'view-coaches'];

                if (in_array($page, $allowed_pages) && file_exists('partials/' . $page . '.php')) {
                    require_once 'partials/' . $page . '.php';
                } else {
                    echo "<h2>Welcome to your dashboard, " . htmlspecialchars($user['full_name']) . "!</h2>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../templates/footer.php'; ?>
