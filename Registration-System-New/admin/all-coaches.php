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
?>
<h1 class="text-primary"><i class="fa fa-user-friends"></i> All Coaches<small class="text-warning"> All Coaches List!</small></h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard </a></li>
        <li class="breadcrumb-item active" aria-current="page">All Coaches</li>
    </ol>
</nav>
<table class="table  table-striped table-hover table-bordered" id="data">
    <thead class="thead-dark">
        <tr>
            <th scope="col">SL</th>
            <th scope="col">Full Name</th>
            <th scope="col">Sport</th>
            <th scope="col">Team</th>
            <th scope="col">Experience (Years)</th>
            <th scope="col">Certifications</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT c.*, s.name as sport_name, t.name as team_name FROM `coaches` c LEFT JOIN `sports` s ON c.sport_id = s.id LEFT JOIN `teams` t ON c.team_id = t.id LEFT JOIN `users` u ON c.user_id = u.id WHERE c.user_id IS NULL OR (u.status = 'approved' AND u.role = 'coach') ORDER BY c.`id` DESC";
        $stmt = $db_con->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $i = 1;
        while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo ucwords($row['full_name']); ?></td>
                <td><?php echo ucwords($row['sport_name']); ?></td>
                <td><?php echo ucwords($row['team_name']); ?></td>
                <td><?php echo $row['experience_years']; ?></td>
                <td><?php echo htmlspecialchars($row['certifications']); ?></td>
            </tr>
        <?php $i++;
        }
        $stmt->close();
        ?>

    </tbody>
</table>